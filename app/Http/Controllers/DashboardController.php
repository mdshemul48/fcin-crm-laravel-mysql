<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\ResellerRecharge;
use App\Services\ExpenseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    public function index()
    {
        // Client Statistics
        $clientStats = $this->getClientStatistics();

        // Payment Collections
        $paymentStats = $this->getPaymentStatistics();

        // Reseller Recharge Statistics
        $rechargeStats = $this->getResellerRechargeStatistics();

        // Expense Statistics
        $expenseStats = $this->getExpenseStatistics();

        // Backup Information
        $backupInfo = $this->getBackupInformation();

        return view('dashboard', array_merge(
            $clientStats,
            $paymentStats,
            $rechargeStats,
            $expenseStats,
            $backupInfo
        ));
    }

    private function getClientStatistics()
    {
        return [
            'totalClients' => Client::count(),
            'paidClients' => Client::where('status', 'paid')->count(),
            'unpaidClients' => Client::where('status', 'due')->count(),
            'totalDue' => Client::where('status', 'due')->sum('due_amount'),
        ];
    }

    private function getPaymentStatistics()
    {
        $paymentCollections = Payment::selectRaw('collected_by, SUM(amount) as total_amount')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->groupBy('collected_by')
            ->with('user')
            ->get();

        $paymentCollectionsDaily = Payment::selectRaw('collected_by, SUM(amount) as total_amount')
            ->whereDate('created_at', now())
            ->groupBy('collected_by')
            ->with('user')
            ->get();

        return [
            'totalPaymentCollectionsThisMonth' => $paymentCollections->sum('total_amount'),
            'paymentCollectedToday' => $paymentCollectionsDaily->sum('total_amount'),
            'paymentCollections' => $paymentCollections,
            'paymentCollectionsDaily' => $paymentCollectionsDaily,
            'monthly' => $paymentCollections->keyBy('collected_by'),
            'daily' => $paymentCollectionsDaily->keyBy('collected_by'),
            'userIds' => $paymentCollections->pluck('collected_by')
                ->merge($paymentCollectionsDaily->pluck('collected_by'))
                ->unique(),
            'latestPayments' => Payment::with(['user', 'client'])
                ->latest()
                ->limit(25)
                ->get(),
        ];
    }

    private function getResellerRechargeStatistics()
    {
        $now = now();
        $monthlyRecharges = ResellerRecharge::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->get();

        $resellerStats = ResellerRecharge::with('reseller')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->selectRaw('reseller_id,
                        SUM(amount) as total_amount,
                        SUM(commission) as total_commission,
                        COUNT(*) as recharge_count')
            ->groupBy('reseller_id')
            ->get();

        return [
            'monthlyRecharges' => $monthlyRecharges->sum('amount'),
            'monthlyCommission' => $monthlyRecharges->sum('commission'),
            'totalRechargeCount' => $monthlyRecharges->count(),
            'averageCommission' => $monthlyRecharges->count() > 0
                ? $monthlyRecharges->avg('commission')
                : 0,
            'resellerStats' => $resellerStats,
        ];
    }

    private function getExpenseStatistics()
    {
        return [
            'currentMonthExpenses' => $this->expenseService->getCurrentMonthTotalExpenses(),
            'previousMonthExpenses' => $this->expenseService->getPreviousMonthTotalExpenses(),
            'expensesByUser' => $this->expenseService->getExpensesByUser(
                now()->startOfMonth()->format('Y-m-d'),
                now()->endOfMonth()->format('Y-m-d')
            ),
        ];
    }

    private function getBackupInformation()
    {
        $disk = Storage::disk('private');
        $backupFiles = collect($disk->files('backup'))
            ->filter(fn($file) => str_ends_with($file, '.zip'))
            ->sortByDesc(fn($file) => $disk->lastModified($file));

        $latestBackup = $backupFiles->first();

        return [
            'backupInfo' => $latestBackup ? [
                'date' => date('Y-m-d H:i', $disk->lastModified($latestBackup)),
                'size' => round($disk->size($latestBackup) / 1048576, 2),
                'filename' => $latestBackup,
                'formatted_size' => round($disk->size($latestBackup) / 1048576, 2) . ' MB',
                'disk' => 'private'
            ] : null,
            'commandStatus' => [
                'status' => $latestBackup ? 'Success' : 'No Backup',
                'message' => $latestBackup ? 'Last backup completed successfully' : 'No backup found'
            ],
            'allBackups' => $backupFiles->map(fn($file) => [
                'date' => date('Y-m-d H:i', $disk->lastModified($file)),
                'size' => round($disk->size($file) / 1048576, 2) . ' MB (' . round($disk->size($file) / 1024, 2) . ' KB)',
                'filename' => basename($file),
                'age' => Carbon::createFromTimestamp($disk->lastModified($file))->diffForHumans(),
            ])->toArray()
        ];
    }
}
