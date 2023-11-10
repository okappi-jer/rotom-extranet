<?php

namespace App\Console\Commands;

use App\Jobs\FetchOutstandingForAdministration;
use App\Models\Administration;
use App\Models\Outstanding;
use App\Models\InvoicePaymentDetail;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

class FetchOutstandingsCommand extends Command
{
    protected $signature = 'outstandings:fetch';
    protected $description = 'Fetch outstanding debtors for all administrations via Yuki';

    public function handle(): int
    {
        $runId = (string) Str::uuid();
        $jobs = [];

        Outstanding::truncate();
        InvoicePaymentDetail::truncate();

        // stream rows to avoid loading all into memory
        foreach (Administration::whereNotNull('token')->cursor() as $admin) {
            $jobs[] = new FetchOutstandingForAdministration(
                token: $admin->token,
                administrationName: $admin->name,
                runId: $runId
            );
        }

        if (empty($jobs)) {
            $this->info('No administrations with tokens found.');
            return self::SUCCESS;
        }

        Bus::batch($jobs)
            ->name('Yuki Outstandings '.$runId)
            ->onQueue('yuki')                 // optional: dedicated queue
            ->allowFailures()
            ->then(function (Batch $batch) use ($runId) {
                \Log::info("Outstandings batch {$batch->id} complete (run_id={$runId}).");
                // OPTIONAL: prune old runs here if you’re keeping snapshots by run_id
            })
            ->catch(function (\Throwable $e) {
                \Log::error('Outstandings batch failed: '.$e->getMessage());
            })
            ->dispatch();

        $this->info('Batch dispatched. run_id='.$runId);
        return self::SUCCESS;
    }
}
