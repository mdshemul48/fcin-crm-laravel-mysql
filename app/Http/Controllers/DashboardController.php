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
            'activeClients' => Client::where('billing_status', true)->count(),
            'inactiveClients' => Client::where('billing_status', false)->count(),
            'paidClients' => Client::where('status', 'paid')->count(),
            'unpaidClients' => Client::where('status', 'due')->count(),
            'totalDue' => Client::where('status', 'due')->sum('due_amount'),
        ];
    }

    private function getPaymentStatistics()
    {
        // Calculate the current billing period (14th to 14th)
        $now = now();
        $currentPeriodStart = $now->copy()->day(14);

        // If we're before the 14th of this month, the billing period started on the 14th of last month
        if ($now->day < 14) {
            $currentPeriodStart->subMonth();
        }

        $currentPeriodEnd = $currentPeriodStart->copy()->addMonth();

        $paymentCollections = Payment::selectRaw('collected_by, SUM(amount) as total_amount')
            ->where('created_at', '>=', $currentPeriodStart)
            ->where('created_at', '<', $currentPeriodEnd)
            ->groupBy('collected_by')
            ->with('user')
            ->get();

        $paymentCollectionsDaily = Payment::selectRaw('collected_by, SUM(amount) as total_amount')
            ->whereDate('created_at', now())
            ->groupBy('collected_by')
            ->with('user')
            ->get();

        $periodLabel = $currentPeriodStart->format('M d') . ' - ' . $currentPeriodEnd->format('M d');

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
            'currentBillingPeriod' => $periodLabel,
        ];
    }

    private function getResellerRechargeStatistics()
    {
        $now = now();
        $currentPeriodStart = $now->copy()->day(14);

        // If we're before the 14th of this month, the billing period started on the 14th of last month
        if ($now->day < 14) {
            $currentPeriodStart->subMonth();
        }

        $currentPeriodEnd = $currentPeriodStart->copy()->addMonth();

        $monthlyRecharges = ResellerRecharge::where('created_at', '>=', $currentPeriodStart)
            ->where('created_at', '<', $currentPeriodEnd)
            ->get();

        $resellerStats = ResellerRecharge::with('reseller')
            ->where('created_at', '>=', $currentPeriodStart)
            ->where('created_at', '<', $currentPeriodEnd)
            ->selectRaw('reseller_id,
                        SUM(amount) as total_amount,
                        SUM(commission) as total_commission,
                        COUNT(*) as recharge_count')
            ->groupBy('reseller_id')
            ->get();

        $periodLabel = $currentPeriodStart->format('M d') . ' - ' . $currentPeriodEnd->format('M d');

        return [
            'monthlyRecharges' => $monthlyRecharges->sum('amount'),
            'monthlyCommission' => $monthlyRecharges->sum('commission'),
            'totalRechargeCount' => $monthlyRecharges->count(),
            'averageCommission' => $monthlyRecharges->count() > 0
                ? $monthlyRecharges->avg('commission')
                : 0,
            'resellerStats' => $resellerStats,
            'rechargesPeriod' => $periodLabel,
        ];
    }

    private function getExpenseStatistics()
    {
        $now = now();
        $currentPeriodStart = $now->copy()->day(14);

        // If we're before the 14th of this month, the billing period started on the 14th of last month
        if ($now->day < 14) {
            $currentPeriodStart->subMonth();
        }

        $currentPeriodEnd = $currentPeriodStart->copy()->addMonth();
        $previousPeriodStart = $currentPeriodStart->copy()->subMonth();
        $previousPeriodEnd = $currentPeriodStart;

        $currentPeriodLabel = $currentPeriodStart->format('M d') . ' - ' . $currentPeriodEnd->format('M d');
        $previousPeriodLabel = $previousPeriodStart->format('M d') . ' - ' . $previousPeriodEnd->format('M d');

        return [
            'currentMonthExpenses' => $this->expenseService->getTotalExpensesByDateRange(
                $currentPeriodStart->format('Y-m-d'),
                $currentPeriodEnd->format('Y-m-d')
            ),
            'previousMonthExpenses' => $this->expenseService->getTotalExpensesByDateRange(
                $previousPeriodStart->format('Y-m-d'),
                $previousPeriodEnd->format('Y-m-d')
            ),
            'expensesByUser' => $this->expenseService->getExpensesByUser(
                $currentPeriodStart->format('Y-m-d'),
                $currentPeriodEnd->format('Y-m-d')
            ),
            'currentExpensePeriod' => $currentPeriodLabel,
            'previousExpensePeriod' => $previousPeriodLabel,
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
