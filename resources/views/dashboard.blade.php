@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_content')
    <div class="d-flex align-items-center">
        <a href="{{ route('clients.create') }}" class="btn btn-dark">
            <i class="bi bi-plus-lg me-1"></i> Add Client
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

        .badge.bg-success {
            background-color: rgba(25, 135, 84, 0.1) !important;
            color: #198754 !important;
        }
    </style>
    <style>
        .card {
            border-radius: 1rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

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

        /* New gradient for Today's Collection card */
        .bg-gradient-secondary {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
        }

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
            text-transform: uppercase;
            font-size: 0.825rem;
            letter-spacing: 0.5px;
            color: #495057;
        }

        .custom-table td {
            vertical-align: middle;
            font-size: 0.875rem;
            color: #6c757d;
        }

        .custom-table tbody tr {
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .user-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.05);
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

        /* Card enhancements */
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
            // Initialize bootstrap modal
            var backupModal = new bootstrap.Modal(document.getElementById('backupDetailsModal'));
        });
    </script>
@endsection
