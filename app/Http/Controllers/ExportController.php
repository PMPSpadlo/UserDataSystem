<?php

namespace App\Http\Controllers;

use App\Models\ImportedUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExportController extends Controller
{
    public function exportUsers(Request $request)
    {
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:1000',
        ]);

        $limit = $validated['limit'] ?? 500;
        $userId = Auth::id();
        $timestamp = now()->format('Ymd_His');
        $filePaths = [];
        $partIndex = 1;

        ImportedUser::chunk($limit, function ($users) use (&$filePaths, $userId, $timestamp, &$partIndex) {
            $csvData = $this->generateCsv($users);
            $fileName = "user_{$userId}_export_{$timestamp}_part_{$partIndex}.csv";
            $filePath = "exports/{$fileName}";
            $filePathGet = "private/exports/{$fileName}";
            Storage::put($filePath, $csvData);
            $filePaths[] = $filePathGet;
            $partIndex++;
        });

        if (empty($filePaths)) {
            return back()->with('status', 'Brak użytkowników do wyeksportowania.');
        }

        if (count($filePaths) === 1) {
            return response()->download(storage_path("app/private" . $filePaths[0]));
        }

        $zipName = "user_{$userId}_export_{$timestamp}.zip";
        $zipFullPath = storage_path("app/exports/{$zipName}");

        if (!Storage::exists('exports')) {
            Storage::makeDirectory('exports');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($filePaths as $path) {
                $localName = basename($path);
                $zip->addFile(storage_path("app/" . $path), $localName);
            }
            $zip->close();
        }

        return response()->download($zipFullPath)->deleteFileAfterSend();
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
