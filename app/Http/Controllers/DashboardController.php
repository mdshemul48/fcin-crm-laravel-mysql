<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $paidClients = Client::where('status', 'paid')->count();
        $unpaidClients = Client::where('status', 'due')->count();
        $totalDue = Client::where('status', 'due')->sum('due_amount');

        $totalPaymentCollectionsThisMonth = Payment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $paymentCollections = Payment::selectRaw('collected_by, SUM(amount) as total_amount')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('collected_by')
            ->with('user')
            ->get();

        // Add this to create the $monthly collection
        $monthly = $paymentCollections->keyBy('collected_by');

        // New daily collections by user
        $paymentCollectionsDaily = Payment::selectRaw('collected_by, SUM(amount) as total_amount')
            ->whereDate('created_at', now())
            ->groupBy('collected_by')
            ->with('user')
            ->get();

        // Add this to create the $daily collection
        $daily = $paymentCollectionsDaily->keyBy('collected_by');

        // Add this new line to fix undefined variable
        $userIds = $paymentCollections->pluck('collected_by')
            ->merge($paymentCollectionsDaily->pluck('collected_by'))
            ->unique();

        // New variable: today's total collection
        $paymentCollectedToday = Payment::whereDate('created_at', now())->sum('amount');

        // New variable: latest 20 payments with client info
        $latestPayments = Payment::with(['user', 'client'])
            ->orderBy('created_at', 'desc')
            ->limit(25)
            ->get();

        $backupStatus = \Cache::get('backup_status', '');
        $backupInfo = \Cache::get('backup_info', null);
        $commandStatus = \Cache::get('command_status', ['status' => 'Idle', 'message' => '']);

        // Get backups from the last week
        $weeklyBackups = Cache::get('weekly_backups', []);

        return view('dashboard', compact(
            'totalClients',
            'paidClients',
            'unpaidClients',
            'totalDue',
            'totalPaymentCollectionsThisMonth',
            'paymentCollections',
            'paymentCollectionsDaily',
            'paymentCollectedToday',
            'latestPayments',
            'backupStatus',
            'backupInfo',
            'commandStatus',
            'weeklyBackups',
            'userIds',
            'monthly', // Add this
            'daily'    // Add this
        ));
    }
}
