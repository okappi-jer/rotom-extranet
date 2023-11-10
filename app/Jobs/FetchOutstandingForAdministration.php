<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FetchOutstandingForAdministration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $timeout = 120;     // keep well under 300s
    public int $tries = 3;
    public array $backoff = [10, 60, 180];

    public function __construct(
        public string $token,
        public string $administrationName,
        public string $runId
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) return;

        $authKey = $this->authenticateWithYuki($this->token);
        $json    = $this->fetchOutstandingDebitorsFromYuki($authKey);

        \Log::info($json);

        $payload = json_decode($json, true) ?? [];
        $today = new \DateTimeImmutable('today');

        $totals = [];
        $details = [];
        
        foreach (($payload['Item'] ?? []) as $item) {
            if (is_array($item['VATNumber'] ?? null)) continue;

            $dueDate = !is_array($item['DueDate'] ?? null)
                ? new \DateTimeImmutable($item['DueDate'])
                : $today;

            $vat  = (string) ($item['VATNumber'] ?? '');
            $open = (float) ($item['OpenAmount'] ?? 0.0);
            $due  = $today >= $dueDate ? $open : 0.0;

            $key = $this->administrationName.'|'.$vat;
            $totals[$key] ??= [
                'invoice_from'       => $this->administrationName,
                'invoice_to'         => $vat,
                'amount_outstanding' => 0.0,
                'amount_due'         => 0.0,
                //'run_id'             => $this->runId,
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
            $totals[$key]['amount_outstanding'] += $open;
            $totals[$key]['amount_due']         += $due;

            if (!is_array($item['Reference'] ?? null)) {
                $details[] = [
                    'invoice_number' => (string) $item['Reference'],
                    'open_amount'    => $open,
                    //'run_id'         => $this->runId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }
        }

        DB::transaction(function () use ($totals, $details) {
            if ($totals) {
                DB::table('outstandings')->upsert(
                    array_values($totals),
                    ['invoice_from', 'invoice_to'],
                    ['amount_outstanding','amount_due',/*'run_id',*/'updated_at']
                );
            }
            if ($details) {
                DB::table('invoice_payment_details')->upsert(
                    $details,
                    ['invoice_number'],
                    ['open_amount',/*'run_id',*/'updated_at']
                );
            }
        });
    }

    // ---------------- SOAP helpers kept INSIDE the Job ----------------

    private function authenticateWithYuki(string $token): string
    {
        $xmlBody = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:they="http://www.theyukicompany.com/">
          <soapenv:Header/>
          <soapenv:Body>
            <they:Authenticate>
              <they:accessKey>{$token}</they:accessKey>
            </they:Authenticate>
          </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $client = new Client(['timeout' => 30]);

        try {
            $resp = $client->post(
                config('services.yuki.auth_url', 'https://api.yukiworks.be/ws/Sales.asmx?WSDL=null'),
                ['headers' => ['Content-Type' => 'text/xml'], 'body' => $xmlBody]
            );

            $xml = new \SimpleXMLElement($resp->getBody()->getContents());
            $ns = $xml->getNamespaces(true);
            $soapNs = $ns['soap'] ?? $ns['soapenv'] ?? 'http://schemas.xmlsoap.org/soap/envelope/';
            $body = $xml->children($soapNs)->Body ?? null;
            if (!$body) throw new \RuntimeException('SOAP Body not found');

            $defaultNs = $ns[''] ?? 'http://www.theyukicompany.com/';
            $respNode = $body->children($defaultNs)->AuthenticateResponse ?? null;
            if (!$respNode) throw new \RuntimeException('AuthenticateResponse not found');

            $authToken = (string) $respNode->AuthenticateResult;
            if ($authToken === '') throw new \RuntimeException('Empty authentication token');

            return $authToken;
        } catch (RequestException $e) {
            $payload = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::error('Yuki Authenticate failed', ['payload' => $payload]);
            throw $e; // let the Job retry
        }
    }

    private function fetchOutstandingDebitorsFromYuki(string $authKey): string
    {
        $xmlBody = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:they="http://www.theyukicompany.com/">
        <soapenv:Header/>
        <soapenv:Body>
            <they:OutstandingDebtorItems>
            <they:sessionID>{$authKey}</they:sessionID>
            <they:includeBankTransactions>true</they:includeBankTransactions>
            <they:sortOrder>DateAsc</they:sortOrder>
            </they:OutstandingDebtorItems>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $endpoint = config('services.yuki.accounting_url', 'https://api.yukiworks.be/ws/Accounting.asmx');

        $client = new \GuzzleHttp\Client(['timeout' => 60]);

        try {
            $resp = $client->post($endpoint, [
                'headers' => [
                    'Content-Type' => 'text/xml; charset=utf-8',
                    // SOAP 1.1 action for .asmx:
                    'SOAPAction'   => '"http://www.theyukicompany.com/OutstandingDebtorItems"',
                ],
                'body' => $xmlBody,
            ]);

            $xmlContent = (string) $resp->getBody();

            $xml = simplexml_load_string($xmlContent);
            if ($xml === false) {
                throw new \RuntimeException('Invalid SOAP XML');
            }

            // Detect SOAP Faults early
            $fault = $xml->xpath('//*[local-name()="Fault"]');
            if ($fault) {
                throw new \RuntimeException('SOAP Fault: '.trim($fault[0]->asXML()));
            }

            // Find the Result node ignoring namespaces
            $resultNodes = $xml->xpath('//*[local-name()="OutstandingDebtorItemsResponse"]/*[local-name()="OutstandingDebtorItemsResult"]');
            if (!$resultNodes || !isset($resultNodes[0])) {
                throw new \RuntimeException('OutstandingDebtorItemsResult not found');
            }
            $result = $resultNodes[0];

            // Some services return inner XML as text/CDATA inside the Result.
            $innerStr = trim((string) $result);
            $innerXml = null;

            if ($innerStr !== '' && strpos($innerStr, '<') !== false) {
                // Parse inner XML string
                $innerXml = simplexml_load_string($innerStr);
                if ($innerXml === false) {
                    throw new \RuntimeException('Failed to parse inner XML inside Result');
                }
            } else {
                // Or the Result already contains nested XML
                $innerXml = $result;
            }

            // Extract Item nodes (ignore namespaces)
            $itemNodes = $innerXml->xpath('//*[local-name()="OutstandingDebtorItems"]/*[local-name()="Item"]');
            $items = [];

            foreach ($itemNodes as $node) {
                $items[] = json_decode(json_encode($node), true);
            }

            // Helpful debug count (remove if noisy)
            \Log::info('Yuki parsed items', ['count' => count($items)]);

            // Return a consistent shape
            return json_encode(['Item' => $items], JSON_UNESCAPED_UNICODE);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $payload = $e->hasResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            \Log::error('Yuki Outstanding HTTP error', ['payload' => $payload]);
            throw $e; // let the job retry
        } catch (\Throwable $e) {
            \Log::error('Yuki Outstanding parse error', ['error' => $e->getMessage()]);
            throw $e; // let the job retry
        }
    }

}
