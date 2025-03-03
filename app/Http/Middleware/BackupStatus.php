<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BackupStatus
{
    public function handle(Request $request, Closure $next)
    {
        $backupInfo = Cache::get('backup_info', [
            'status' => 'No Backup',
            'last_backup' => null,
            'size' => null
        ]);

        $request->attributes->add(['backup_info' => $backupInfo]);
        return $next($request);
    }
}
