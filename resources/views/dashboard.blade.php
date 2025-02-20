@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_content')
    <div class="d-flex align-items-center">
        <a href="{{ route('expenses.index') }}" class="btn btn-dark">
            <i class="bi bi-plus-lg me-1"></i> Add Expense
        </a>

        <div class="backup-status-btn ms-2" onclick="$('#backupDetailsModal').modal('show')">
            <span class="status-indicator {{ $commandStatus['status'] === 'Failed' ? 'bg-danger' : 'bg-success' }}"></span>
            <span class="d-none d-md-inline">Backup Status</span>
            @if ($backupInfo)
                <small
                    class="text-muted d-none d-lg-inline">{{ isset($backupInfo['date']) ? \Carbon\Carbon::parse($backupInfo['date'])->format('d/m/y') : 'N/A' }}</small>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid mt-4 mb-3">
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Total Clients</h6>
                                <h4 class="fw-bold mb-0">{{ $totalClients }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Paid Clients</h6>
                                <h4 class="fw-bold mb-0">{{ $paidClients }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-danger text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Unpaid Clients</h6>
                                <h4 class="fw-bold mb-0">{{ $unpaidClients }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-exclamation-circle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Collection Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Today's Collection</h6>
                                <h4 class="fw-bold mb-0">৳{{ number_format($paymentCollectedToday, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-calendar-day fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collections This Month Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Collections This Month</h6>
                                <h4 class="fw-bold mb-0">৳{{ number_format($totalPaymentCollectionsThisMonth, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-money-bill-wave fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Due Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Total Due</h6>
                                <h4 class="fw-bold mb-0">৳{{ number_format($totalDue, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-hand-holding-usd fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Month Expenses Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-purple text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">This Month's Expenses</h6>
                                <h4 class="fw-bold mb-0">৳{{ number_format($currentMonthExpenses, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-file-invoice fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Previous Month Expenses Card -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-indigo text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Last Month's Expenses</h6>
                                <h4 class="fw-bold mb-0">৳{{ number_format($previousMonthExpenses, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-history fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reseller Stats -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 bg-gradient-violet text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Month's Recharges</h6>
                                <h4 class="fw-bold mb-0">৳{{ number_format($monthlyRecharges ?? 0, 2) }}</h4>
                                <small class="text-white-50">{{ $totalRechargeCount ?? 0 }} recharges this month</small>
                            </div>
                            <div>
                                <i class="fas fa-exchange-alt fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 bg-gradient-pink text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Month's Commission</h6>
                                <h4 class="fw-bold mb-0">৳{{ number_format($monthlyCommission ?? 0, 2) }}</h4>
                                <small class="text-white-50">Avg:
                                    ৳{{ number_format($averageCommission ?? 0, 2) }}/recharge</small>
                            </div>
                            <div>
                                <i class="fas fa-percentage fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Tables Section -->
        <div class="row g-3">
            <div class="col-lg-6 col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-dark text-white py-3">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-line me-2"></i>Payment Collections by User
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 py-3">User</th>
                                        <th class="text-end py-3">Monthly Collections</th>
                                        <th class="text-end pe-4 py-3">Today's Collections</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userIds as $userId)
                                        @php
                                            $monthlyAmount = $monthly->has($userId)
                                                ? $monthly[$userId]->total_amount
                                                : 0;
                                            $dailyAmount = $daily->has($userId) ? $daily[$userId]->total_amount : 0;
                                            $name = $monthly->has($userId)
                                                ? $monthly[$userId]->user->name
                                                : ($daily->has($userId)
                                                    ? $daily[$userId]->user->name
                                                    : 'Unknown');
                                        @endphp
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="user-icon me-2">
                                                        <i class="fas fa-user-circle text-primary"></i>
                                                    </div>
                                                    <span>{{ $name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end py-3">৳{{ number_format($monthlyAmount, 2) }}</td>
                                            <td class="text-end pe-4 py-3">৳{{ number_format($dailyAmount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th class="ps-4 py-3">Total</th>
                                        <th class="text-end py-3">
                                            ৳{{ number_format($paymentCollections->sum('total_amount'), 2) }}</th>
                                        <th class="text-end pe-4 py-3">
                                            ৳{{ number_format($paymentCollectionsDaily->sum('total_amount'), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-dark text-white py-3">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-receipt me-2"></i>Latest Payments
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-hover custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 py-3">P.ID</th>
                                        <th class="py-3">User</th>
                                        <th class="py-3">Client</th>
                                        <th class="text-end py-3">Amount</th>
                                        <th class="text-end pe-4 py-3">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestPayments as $payment)
                                        <tr>
                                            <td class="ps-4 py-3">#{{ $payment->id }}</td>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="user-icon me-2">
                                                        <i class="fas fa-user-circle text-secondary"></i>
                                                    </div>
                                                    <span>{{ $payment->user->name }}</span>
                                                </div>
                                            </td>
                                            <td class="py-3">{{ $payment->client->username ?? 'N/A' }}</td>
                                            <td class="text-end py-3">৳{{ number_format($payment->amount, 2) }}</td>
                                            <td class="text-end pe-4 py-3">
                                                <span class="badge bg-light text-dark">
                                                    {{ $payment->created_at->format('h:iA d/m/y') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-dark text-white py-3">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-file-invoice me-2"></i>Month's Expenses by User
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 py-3">User</th>
                                        <th class="text-end py-3">Amount</th>
                                        <th class="text-center py-3">Count</th>
                                        <th class="text-end pe-4 py-3">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expensesByUser as $userExpense)
                                        <tr>
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="user-icon me-2">
                                                        <i class="fas fa-user-circle text-primary"></i>
                                                    </div>
                                                    <span>{{ $userExpense->createdBy->name }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end py-3">৳{{ number_format($userExpense->total_amount, 2) }}
                                            </td>
                                            <td class="text-center py-3">{{ $userExpense->count }}</td>
                                            <td class="text-end pe-4 py-3">
                                                {{ number_format(($userExpense->total_amount / $currentMonthExpenses) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th class="ps-4 py-3">Total</th>
                                        <th class="text-end py-3">৳{{ number_format($currentMonthExpenses, 2) }}</th>
                                        <th class="text-center py-3">{{ $expensesByUser->sum('count') }}</th>
                                        <th class="text-end pe-4 py-3">100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-dark text-white py-3">
                        <h5 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-chart-pie me-2"></i>Monthly Recharges by Reseller
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Reseller</th>
                                        <th class="text-end">Total Recharge</th>
                                        <th class="text-end">Total Commission</th>
                                        <th class="text-center pe-4">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($resellerStats as $stat)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ strtoupper(substr($stat->reseller->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $stat->reseller->name }}</h6>
                                                        <small class="text-muted">{{ $stat->reseller->phone }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">৳{{ number_format($stat->total_amount, 2) }}</td>
                                            <td class="text-end">৳{{ number_format($stat->total_commission, 2) }}</td>
                                            <td class="text-center pe-4">
                                                <span class="badge bg-light text-dark">
                                                    {{ $stat->recharge_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="bi bi-wallet2 display-4 text-muted"></i>
                                                    <p class="mt-3">No recharges this month</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <th class="ps-4">Total</th>
                                        <th class="text-end">৳{{ number_format($monthlyRecharges, 2) }}</th>
                                        <th class="text-end">৳{{ number_format($monthlyCommission, 2) }}</th>
                                        <th class="text-center pe-4">{{ $totalRechargeCount }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Backup Details Modal -->
        <div class="modal fade" id="backupDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div
                        class="modal-header {{ $commandStatus['status'] === 'Failed' ? 'bg-danger' : 'bg-success' }} text-white">
                        <h5 class="modal-title">
                            <i
                                class="fas {{ $commandStatus['status'] === 'Failed' ? 'fa-exclamation-circle' : 'fa-server' }} me-2"></i>
                            Backup Status
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="backup-details">
                            <div class="current-status mb-4">
                                <h6 class="text-muted mb-3">Current Status</h6>
                                <div class="d-flex align-items-center">
                                    <span
                                        class="status-dot {{ $commandStatus['status'] === 'Failed' ? 'bg-danger' : 'bg-success' }}"></span>
                                    <div class="ms-3">
                                        <h5 class="mb-1">{{ $commandStatus['status'] }}</h5>
                                        <p class="mb-0 text-muted">{{ $commandStatus['message'] }}</p>
                                    </div>
                                </div>
                            </div>

                            @if ($backupInfo)
                                <h6 class="text-muted mb-3">Latest Backup Details</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="backup-stat-card">
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                            <div>
                                                <small>Date</small>
                                                <h6>{{ $backupInfo['date'] }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="backup-stat-card">
                                            <i class="fas fa-hdd text-success"></i>
                                            <div>
                                                <small>Size</small>
                                                <h6>{{ $backupInfo['formatted_size'] }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="backup-stat-card">
                                            <i class="fas fa-database text-info"></i>
                                            <div>
                                                <small>Storage</small>
                                                <h6>{{ ucfirst($backupInfo['disk']) }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="backup-stat-card">
                                            <i class="fas fa-file-archive text-warning"></i>
                                            <div>
                                                <small>Filename</small>
                                                <h6 class="text-truncate">{{ $backupInfo['filename'] }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Table Styles */
        .table thead th {
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .table tbody td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        /* Card Styles */
        .card {
            border-radius: 1rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e, #38ef7d);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f7971e, #ffd200);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8, #0dcaf0);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
        }

        .bg-gradient-purple {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
        }

        .bg-gradient-indigo {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .bg-gradient-violet {
            background: linear-gradient(135deg, #8e44ad, #9b59b6);
        }

        .bg-gradient-pink {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        /* Additional Styles */
        h6 {
            font-size: 0.875rem;
        }

        .backup-status-indicator {
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .backup-status-indicator:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .backup-details .detail-item {
            display: flex;
            align-items: start;
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .backup-details .detail-item:last-child {
            border-bottom: none;
        }

        .backup-details .detail-item i {
            font-size: 1.5rem;
            margin-right: 1rem;
            width: 2rem;
            text-align: center;
        }

        .backup-details .detail-item h6 {
            margin: 0;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .backup-details .detail-item p {
            margin: 0;
            font-size: 1rem;
        }

        .backup-status-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #dee2e6;
        }

        .backup-status-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .backup-stat-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
        }

        .backup-stat-card i {
            font-size: 1.5rem;
        }

        .backup-stat-card small {
            color: #6c757d;
            display: block;
            margin-bottom: 0.25rem;
        }

        .backup-stat-card h6 {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Enhanced Table Styles */
        .custom-table {
            margin-bottom: 0;
        }

        .custom-table thead tr {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .custom-table th {
            font-weight: 600;
        }

        .custom-table tbody tr {
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .custom-table td {
            vertical-align: middle;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .user-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.02);
        }

        .user-icon i {
            font-size: 1.2rem;
        }

        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #212529, #343a40);
        }

        .card {
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .card-header {
            border-bottom: none;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var backupModal = new bootstrap.Modal(document.getElementById('backupDetailsModal'));
        });
    </script>
@endsection
