<?php

namespace App\Http\Controllers;

use App\Models\Outstanding;
use App\Models\InvoicePaymentDetail;
use App\Models\Invoice;
use App\Models\Administration;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class YukiController extends Controller
{
    function authenticateWithYuki($apiKey)
    {
        $xmlBody = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:they="http://www.theyukicompany.com/">
        <soapenv:Header/>
        <soapenv:Body>
            <they:Authenticate>
                <they:accessKey>{$apiKey}</they:accessKey>
            </they:Authenticate>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $client = new Client();

        try {
            $response = $client->post('https://api.yukiworks.be/ws/Sales.asmx?WSDL=null', [
                'headers' => [
                    'Content-Type' => 'text/xml',
                ],
                'body' => $xmlBody,
            ]);

            $responseBody = $response->getBody()->getContents();
            $xml = new \SimpleXMLElement($responseBody);
            $namespaces = $xml->getNamespaces(true);
            $body = $xml->children($namespaces['soap'])->Body;
            $responseContent = $body->children($namespaces[''])->AuthenticateResponse;
            $authToken = (string) $responseContent->AuthenticateResult;

            return $authToken;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse()->getBody()->getContents();
                return "Error: " . $errorResponse;
            } else {
                return "Error: " . $e->getMessage();
            }
        }
    }

    function getAdministrationId()
    {
        $authenticationKey = $this->authenticateWithYuki(env('MIX_YUKI_ROTOM'));

        $url = 'https://api.yukiworks.be/ws/Sales.asmx?WSDL=null';

        $headers = [
            'Content-Type' => 'text/xml',
        ];

        $body = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:they="http://www.theyukicompany.com/">
            <soapenv:Header/>
            <soapenv:Body>
                <they:Administrations>
                    <they:sessionID>{$authenticationKey}</they:sessionID>
                </they:Administrations>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;

        try {
            $response = \Http::withHeaders($headers)->send('POST', $url, [
                'body' => $body,
            ]);

            if ($response->successful()) {
                return $response->body();
            } else {
                throw new \Exception('Error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (RequestException $e) {
            return 'Request failed: ' . $e->getMessage();
        } catch (\Exception $e) {
            return 'An error occurred: ' . $e->getMessage();
        }
    }

    function showOutstandingDebitors()
    {
        $outstandings = Outstanding::get();

        return response()->json([
            'data' => $outstandings,
        ], 200);
    }

    function showInvoices()
    {
        $invoices = Invoice::latest()->get();

        return response()->json([
            'data' => $invoices,
        ], 200);
    }

    function syncOutstandingDebitors()
    {
        $debitors = $this->getOutstandingDebitors();
        return response()->json(true, 200);
    }

    function getOutstandingDebitors()
    {
        try {
            // First empty the whole table
            Outstanding::truncate();
            InvoicePaymentDetail::truncate();
    
            // Get all administrations 
            $administrations = Administration::get();
    
            // Loop and get data
            foreach ($administrations as $administration) {
                $token = $administration->token;
    
                if ($token) {
                    $authenticationKey = $this->authenticateWithYuki($token);
    
                    if (!$authenticationKey) {
                        \Log::error("Authentication failed for administration: {$administration->name}");
                        return response()->json(['error' => 'Authentication failed'], 401);
                    }
    
                    try {
                        $this->getOutstandingDebitorsByAdministration($authenticationKey, $administration->name);
                    } catch (\Exception $e) {
                        \Log::error("Error processing outstanding debtors for {$administration->name}: " . $e->getMessage());
                        return response()->json(['error' => 'Error processing outstanding debtors'], 500);
                    }
                }
            }
    
            return response()->json(['data' => "Success"], 200);
        } catch (\Exception $e) {
            \Log::error("Error in getOutstandingDebitors: " . $e->getMessage());
            return response()->json(['error' => 'Error in getOutstandingDebitors'], 500);
        }
    }
    
    function getOutstandingDebitorsByAdministration($authenticationKey, $administrationName)
    {
        \Log::info("Start " . $administrationName);

        try {
            $outstandings = $this->fetchOutstandingDebitorsFromYuki($authenticationKey);
            $items = json_decode($outstandings, true);
    
            foreach ($items['Item'] as $item) {
                if (!is_array($item['VATNumber'])) {
                    $today = new \DateTime();
                    $date = $today;
    
                    if (!is_array($item['DueDate'])) {
                        $date = new \DateTime($item['DueDate']);
                    }
    
                    $vat = $item['VATNumber'];
                    $open_amount = $item['OpenAmount'];
                    $due_amount = $today >= $date ? $item['OpenAmount'] : 0;
    
                    $customer_already_present = Outstanding::where('invoice_to', $vat)
                        ->where('invoice_from', $administrationName)
                        ->first();
    
                    if ($customer_already_present) {
                        $amount_outstanding = floatval($customer_already_present->amount_outstanding) + $open_amount;
                        $amount_due = floatval($customer_already_present->amount_due) + $due_amount;
    
                        $customer_already_present->update([
                            'amount_outstanding' => $amount_outstanding,
                            'amount_due' => $amount_due,
                        ]);
                    } else {
                        Outstanding::create([
                            'invoice_from' => $administrationName,
                            'invoice_to' => $vat,
                            'amount_outstanding' => $open_amount,
                            'amount_due' => $due_amount,
                        ]);
                    }
    
                    if (!is_array($item['Reference'])) {
                        InvoicePaymentDetail::create([
                            'invoice_number' => $item['Reference'],
                            'open_amount' => $open_amount,
                        ]);
                    }
                }
            }
    
            return 'Entries created';
        } catch (\Exception $e) {
            \Log::error("Error fetching outstanding debtors for {$administrationName}: " . $e->getMessage());
            throw $e;  // Propagate error to higher level for handling
        }
    }
    
    function fetchOutstandingDebitorsFromYuki($authenticationKey)
    {
        $xmlBody = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:they="http://www.theyukicompany.com/">
        <soapenv:Header/>
        <soapenv:Body>
            <they:OutstandingDebtorItems>
                <they:sessionID>{$authenticationKey}</they:sessionID>
                <they:includeBankTransactions>true</they:includeBankTransactions>
                <they:sortOrder>DateAsc</they:sortOrder>
            </they:OutstandingDebtorItems>
        </soapenv:Body>
        </soapenv:Envelope>
        XML;
    
        $client = new Client(['timeout' => 15]); // Set a 15-second timeout
    
        try {
            $response = $client->post('https://api.yukiworks.be/ws/Accounting.asmx?WSDL=null', [
                'headers' => [
                    'Content-Type' => 'text/xml',
                ],
                'body' => $xmlBody,
            ]);
    
            $xmlContent = $response->getBody()->getContents();
    
            $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);
            $namespaces = $xml->getNamespaces(true);
            $body = $xml->children($namespaces['soap'])->Body;
    
            $response = $body->children(null)->OutstandingDebtorItemsResponse;
            $outstandingDebtorItems = $response->OutstandingDebtorItemsResult->OutstandingDebtorItems;
    
            return json_encode($outstandingDebtorItems);
    
        } catch (RequestException $e) {
            \Log::error("HTTP request error:", [
                'message' => $e->getMessage(),
                'url' => $e->getRequest()->getUri(),
                'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : null,
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
            ]);
    
            return response()->json(['error' => 'HTTP request error: ' . ($e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage())], 500);
        } catch (\Exception $e) {
            \Log::error("General error in fetchOutstandingDebitorsFromYuki: " . $e->getMessage());
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }

    function syncInvoices()
    {
        $invoices = $this->getInvoices();
        return response()->json(true, 200);
    }
    
    function getInvoices()
    {
        // Get all administrations 
        $administrations = Administration::get();

        // Loop and get data
        foreach ($administrations as $administration) {
            $token = $administration->token;

            if ($token) {
                $authenticationKey = $this->authenticateWithYuki($token);

                if (!$authenticationKey) {
                    \Log::error("Authentication failed for administration: {$administration->name}");
                    return response()->json(['error' => 'Authentication failed'], 401);
                }

                // Use try-catch to handle errors in processing each administration
                try {
                    $this->getInvoicesByAdministration($authenticationKey, $administration->name);
                } catch (\Exception $e) {
                    \Log::error("Error processing invoices for {$administration->name}: " . $e->getMessage());
                    return response()->json(['error' => 'Error processing invoices'], 500);
                }
            }
        }

        return response()->json(['data' => "Success"], 200);
    }

    function getInvoicesByAdministration($authenticationKey, $administrationName)
    {
        try {
            $invoices = $this->fetchInvoices($authenticationKey);
            $documents = json_decode($invoices, true);

            foreach ($documents['Document'] as $document) {
                $company = $document['ContactName'];
                $amount_invoice = $document['Amount'];
                $invoice_number = $document['Reference'];

                // Check if already exists in DB
                $in_outstanding_table = InvoicePaymentDetail::where('invoice_number', $invoice_number)->first();

                $amount_paid = $in_outstanding_table
                    ? $amount_invoice - $in_outstanding_table->open_amount
                    : $amount_invoice;

                \Log::info("Processing invoice: {$invoice_number} with amount paid: {$amount_paid}");

                // Check if invoice already created
                $invoice = Invoice::where('invoice_number', $invoice_number)->first();
                $data = [
                    'invoice_to' => $company,
                    'invoice_from' => $administrationName,
                    'invoice_number' => $invoice_number,
                    'amount_invoice' => $amount_invoice,
                    'amount_paid' => $amount_paid,
                ];

                if ($invoice) {
                    $invoice->update($data);
                } else {
                    Invoice::create($data);
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error fetching invoices for administration {$administrationName}: " . $e->getMessage());
            throw $e;  // Rethrow to handle in getInvoices()
        }
    }

    function fetchInvoices($authenticationKey)
    {
        // Get the current date
        $today = now();
        $startDate = $today->copy()->startOfMonth()->toDateString();
        $endDate = $today->toDateString();

        $xmlBody = <<<XML
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:they="http://www.theyukicompany.com/">
            <soapenv:Header/>
            <soapenv:Body>
                <they:DocumentsInTab>
                    <they:sessionID>{$authenticationKey}</they:sessionID>
                    <they:tabID>201</they:tabID>    
                    <they:sortOrder>DocumentDateAsc</they:sortOrder>
                    <they:startDate>{$startDate}</they:startDate>
                    <they:endDate>{$endDate}</they:endDate>
                    <they:numberOfRecords>100000</they:numberOfRecords>
                    <they:startRecord>0</they:startRecord>
                </they:DocumentsInTab>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;

        $client = new Client(['timeout' => 15]); // Set a 15-second timeout

        try {
            // Send the POST request to the API endpoint
            $response = $client->post('https://api.yukiworks.be/ws/Archive.asmx?WSDL=null', [
                'headers' => [
                    'Content-Type' => 'text/xml',
                ],
                'body' => $xmlBody,
            ]);

            $xmlContent = $response->getBody()->getContents();
            $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);
            $namespaces = $xml->getNamespaces(true);
            $body = $xml->children($namespaces['soap'])->Body;

            $response = $body->children(null)->DocumentsInTabResponse;
            $invoices = $response->DocumentsInTabResult->Documents;

            return json_encode($invoices);

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                \Log::error("HTTP error: " . $e->getResponse()->getBody()->getContents());
                return response()->json(['error' => 'HTTP request error'], 500);
            } else {
                \Log::error("Request error: " . $e->getMessage());
                return response()->json(['error' => 'Request error: ' . $e->getMessage()], 500);
            }
        } catch (\Exception $e) {
            \Log::error("General error in fetchInvoices: " . $e->getMessage());
            return response()->json(['error' => 'General error: ' . $e->getMessage()], 500);
        }
    }
}