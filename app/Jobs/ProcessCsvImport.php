<?php

namespace App\Jobs;

use App\Models\Import;
use App\Jobs\ProcessCsvRow;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $importId;
    public $filePath;

    public function __construct(int $importId, string $filePath)
    {
        $this->importId = $importId;
        $this->filePath = $filePath;
    }

    public function handle()
    {
        $import = Import::find($this->importId);

        if (!$import) {
            Log::error("Import ID {$this->importId} not found.");
            return;
        }

        $filePath = Storage::path($this->filePath);

        if (!file_exists($filePath)) {
            Log::error("File {$filePath} not found for import ID {$this->importId}.");
            $import->update(['status' => 'failed']);
            return;
        }

        Log::info("Processing file: {$filePath} for import ID: {$this->importId}");

        $import->update(['status' => 'in_progress', 'pending_tasks' => 0]);

        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            Log::error("Unable to open file for import ID {$this->importId}.");
            $import->update(['status' => 'failed']);
            return;
        }

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            ProcessCsvRow::dispatch($data, $this->importId);
            $import->increment('pending_tasks');
        }

        fclose($handle);
    }
}
