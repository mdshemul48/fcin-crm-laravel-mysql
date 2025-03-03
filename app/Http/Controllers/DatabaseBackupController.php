<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;

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
}
