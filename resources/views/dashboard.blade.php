@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_content')


    <a href="{{ route('clients.create') }}" class="btn btn-dark ms-1">
        <i class="bi bi-plus-lg me-1"></i> Add Client
    </a>
@endsection
@section('content')
    <div class="container-fluid mt-4">
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

            <!-- New card for Today's Collection -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Today's Collection</h6>
                                <h4 class="fw-bold mb-0">${{ number_format($paymentCollectedToday, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-calendar-day fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Collections This Month</h6>
                                <h4 class="fw-bold mb-0">${{ number_format($totalPaymentCollectionsThisMonth, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-money-bill-wave fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow h-100 py-3 bg-gradient-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="text-uppercase fw-bold mb-2">Total Due</h6>
                                <h4 class="fw-bold mb-0">${{ number_format($totalDue, 2) }}</h4>
                            </div>
                            <div>
                                <i class="fas fa-dollar-sign fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New combined table card for Monthly and Daily Payment Collections by User -->
        @php
            $monthly = $paymentCollections->keyBy('collected_by');
            $daily = $paymentCollectionsDaily->keyBy('collected_by');
            $userIds = $monthly->keys()->merge($daily->keys())->unique();
        @endphp
        <div class="row g-1">
            <div class="col-lg-6 col-12 my-2">
                <div class="card shadow">
                    <div class="card-header bg-dark text-light rounded-md">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Payment Collections by User</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4">User</th>
                                        <th class="text-end pe-4">Monthly Collections</th>
                                        <th class="text-end pe-4">Today's Collections</th>
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
                                            <td class="ps-4">
                                                <i class="fas fa-user-circle me-2 text-secondary"></i> {{ $name }}
                                            </td>
                                            <td class="text-end pe-4">${{ number_format($monthlyAmount, 2) }}</td>
                                            <td class="text-end pe-4">${{ number_format($dailyAmount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th class="ps-4">Total</th>
                                        <th class="text-end pe-4">
                                            ${{ number_format($paymentCollections->sum('total_amount'), 2) }}</th>
                                        <th class="text-end pe-4">
                                            ${{ number_format($paymentCollectionsDaily->sum('total_amount'), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12 my-2">
                <div class="card shadow">
                    <div class="card-header bg-dark text-light rounded-md">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i> Latest Payments</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="ps-4">P.ID</th>
                                        <th class="ps-4">User</th>
                                        <th class="text-end pe-4">Client</th>
                                        <th class="text-end pe-4">Amount</th>
                                        <th class="text-end pe-4">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($latestPayments as $payment)
                                        <tr>
                                            <td class="ps-4">{{ $payment->id }}</td>
                                            <td class="ps-4">
                                                <i class="fas fa-user-circle me-2 text-secondary"></i>
                                                {{ $payment->user->name }}
                                            </td>
                                            <td class="text-end pe-4">
                                                {{ $payment->client->username ?? 'N/A' }}
                                            </td>
                                            <td class="text-end pe-4">${{ number_format($payment->amount, 2) }}</td>
                                            <td class="text-end pe-4">{{ $payment->created_at->format('h:iA d/m/y') }}
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
    </style>
@endsection

@section('js')
@endsection
