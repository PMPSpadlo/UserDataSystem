<?php

namespace App\Jobs;

use App\Models\Import;
use App\Services\CsvRecordProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ImportSummaryMail;

class ProcessCsvRow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $rowData;
    public int $importId;

    public function __construct(array $rowData, int $importId)
    {
        $this->rowData = $rowData;
        $this->importId = $importId;
    }

    public function handle()
    {
        try {
            $processor = new CsvRecordProcessor();
            $success = $processor->processRecord($this->rowData, $this->importId);

            $import = Import::find($this->importId);

            if ($success) {
                $import?->increment('success_count');
            } else {
                $import?->increment('error_count');
                Log::warning("Failed to process row for import ID {$this->importId}: " . json_encode($this->rowData));
            }

            $this->decrementPendingTasks($import);
        } catch (\Exception $e) {
            Log::error("Error while processing row for import ID {$this->importId}: " . $e->getMessage());
            $import = Import::find($this->importId);
            $import?->increment('error_count');
            $this->decrementPendingTasks($import);
        }
    }

    private function decrementPendingTasks(?Import $import): void
    {
        if ($import) {
            $import->decrement('pending_tasks');

            if ($import->pending_tasks === 0) {
                $import->update(['status' => 'completed']);

                try {
                    Mail::to('pmp.spadlo@gmail.com')->send(new ImportSummaryMail($import));
                    Log::info("Import summary email sent for import ID {$this->importId}.");
                } catch (\Exception $e) {
                    Log::error("Failed to send import summary email for import ID {$this->importId}: " . $e->getMessage());
                }
            }
        }
    }

}
