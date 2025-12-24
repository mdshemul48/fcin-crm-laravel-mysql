<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DatabaseBackupController extends Controller
{
    protected $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    public function backup()
    {
        $result = $this->backupService->createBackup();

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'file' => $result['file']
            ]);
        }

        return response()->json(['error' => $result['message']], 500);
    }

    public function download(Request $request)
    {
        $request->validate([
            'file' => 'required|string'
        ]);

        $disk = Storage::disk('private');
        $filePath = $request->input('file');

        // Security check: ensure the file is in the backup directory
        if (!$disk->exists($filePath) || !str_starts_with($filePath, 'backup/')) {
            abort(404, 'Backup file not found');
        }

        // Ensure it's a zip file
        if (!str_ends_with($filePath, '.zip')) {
            abort(400, 'Invalid file type');
        }

        return $disk->download($filePath);
    }
}
