<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\ImportedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function importHistory()
    {
        $imports = Import::orderBy('created_at', 'desc')->paginate(10);
        return view('import_history', compact('imports'));
    }

    public function exportUsers(Request $request)
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:1000',
        ]);

        $limit = $validated['limit'] ?? 1000;
        $users = ImportedUser::limit($limit)->get();

        $csvData = $this->generateCsv($users);
        $userId = Auth::id();
        $fileName = "user_{$userId}_export_" . now()->format('Ymd_His') . '.csv';
        $filePath = "exports/$fileName";

        // Save the file in the user's folder
        Storage::put($filePath, $csvData);

        // Provide the file for immediate download
        return response()->download(storage_path("app/$filePath"))->deleteFileAfterSend(true);
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

    private function generateCsv($users)
    {
        $headers = ['First Name', 'Last Name', 'Email', 'Import ID', 'Created At'];
        $csvData = implode(',', $headers) . "\n";

        foreach ($users as $user) {
            $csvData .= implode(',', [
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->import_id,
                    $user->created_at,
                ]) . "\n";
        }

        return $csvData;
    }
}
