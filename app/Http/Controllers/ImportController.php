<?php

namespace App\Http\Controllers;

use App\Models\Import;
use Illuminate\Http\Request;
use App\Jobs\ProcessCsvImport;

class ImportController extends Controller
{
    public function dashboard()
    {
        $imports = Import::orderBy('created_at', 'desc')->paginate(10);
        return view('admin_dashboard', compact('imports'));
    }

    public function handleUpload(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        try {
            $path = $this->storeFile($request->file('csv_file'));
            $import = $this->createImportRecord($path);
            ProcessCsvImport::dispatch($import->id, $path);

            return redirect()->route('import.history')->with('success', 'File uploaded successfully and is being processed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    private function storeFile($file)
    {
        $path = $file->store('uploads');

        if (!$path) {
            \Log::error('File upload failed. File: ' . $file->getClientOriginalName());
            throw new \Exception('File upload failed.');
        }

        \Log::info('File uploaded successfully. Path: ' . $path);
        return $path;
    }

    private function createImportRecord(string $path)
    {
        return Import::create([
            'file_name' => basename($path),
            'status' => 'in_progress',
        ]);
    }
}
