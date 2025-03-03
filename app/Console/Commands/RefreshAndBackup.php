<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DatabaseBackupService;

class RefreshAndBackup extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Create and email database backup';

    protected $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    public function handle()
    {
        $result = $this->backupService->createBackup();

        if ($result['success']) {
            $this->info('Backup completed successfully.');
            return 0;
        }

        $this->error($result['message']);
        return 1;
    }
}
