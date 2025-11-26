@extends('layouts.app')

@section('title', 'Payment Reports')

@section('css')
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #00c9ff 0%, #92fe9d 100%);
            --warning-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);
            --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-soft: 0 8px 32px rgba(31, 38, 135, 0.37);
            --shadow-hover: 0 15px 35px rgba(31, 38, 135, 0.5);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .page-container {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: var(--glass-bg);
            border-radius: 25px;
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-soft);
            margin: 1rem;
            padding: 2rem;
        }

        .page-header {
            background: var(--primary-gradient);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="50" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="25" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stats-card {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background: var(--glass-bg);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 2rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--success-gradient);
            transition: height 0.3s ease;
        }

        .stats-card.paid::before {
            background: var(--success-gradient);
        }

        .stats-card.unpaid::before {
            background: var(--danger-gradient);
        }

        .stats-card.collection::before {
            background: var(--info-gradient);
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .stats-card:hover::before {
            height: 8px;
        }

        .stats-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .stats-card.paid .stats-icon {
            background: var(--success-gradient);
            box-shadow: 0 10px 30px rgba(0, 201, 255, 0.3);
        }

        .stats-card.unpaid .stats-icon {
            background: var(--danger-gradient);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.3);
        }

        .stats-card.collection .stats-icon {
            background: var(--info-gradient);
            box-shadow: 0 10px 30px rgba(168, 237, 234, 0.3);
        }

        .stats-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .stats-label {
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }

        .filter-section {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background: var(--glass-bg);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
        }

        .report-type-toggle {
            display: inline-flex;
            background: #f8fafc;
            padding: 6px;
            border-radius: 50px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .report-type-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer;
            background: transparent;
            color: #64748b;
            position: relative;
            z-index: 1;
            user-select: none;
            text-decoration: none;
        }

        .report-type-btn.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            transform: scale(1.05);
        }

        .report-type-btn:not(.active):hover {
            color: #475569;
            transform: scale(1.02);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            padding: 12px 18px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }

        input[type="date"] {
            position: relative;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }

        .search-container {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 2;
        }

        .search-input {
            padding-left: 50px !important;
            border-radius: 50px !important;
        }

        .data-table {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background: var(--glass-bg);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .table-header {
            background: var(--dark-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border: none;
        }

        .table-header h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .data-table table {
            margin: 0;
            background: transparent;
        }

        .data-table th {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            padding: 1.25rem 1rem;
            font-size: 0.8rem;
        }

        .data-table td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            background: rgba(255, 255, 255, 0.5);
        }

        .data-table tbody tr {
            transition: all 0.2s ease;
        }

        .data-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.8) !important;
            transform: scale(1.002);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .client-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .client-name {
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .client-id {
            font-family: 'Monaco', 'Menlo', monospace;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #475569;
        }

        .amount-success {
            color: #059669;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .amount-danger {
            color: #dc2626;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 201, 255, 0.3);
        }

        .status-due {
            background: var(--danger-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .action-btn {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 8px 12px;
            color: white;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .export-btn {
            background: var(--success-gradient);
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .export-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 201, 255, 0.4);
            color: white;
        }

        .filter-btn {
            background: var(--primary-gradient);
            border: none;
            border-radius: 15px;
            padding: 12px 20px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #94a3b8;
        }

        .empty-state h4 {
            color: #475569;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .page-container {
                margin: 0.5rem;
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stats-card {
                padding: 1.5rem;
            }

            .stats-value {
                font-size: 2rem;
            }

            .filter-section {
                padding: 1.5rem;
            }

            .report-type-toggle {
                width: 100%;
                justify-content: center;
            }

            .data-table th,
            .data-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }

            .client-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .client-avatar {
                width: 35px;
                height: 35px;
            }
        }

        .report-type-btn.active {
            background: var(--primary-gradient) !important;
            color: white !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
            transform: scale(1.05) !important;
        }

        .btn-check:checked+.report-type-btn {
            background: var(--primary-gradient) !important;
            color: white !important;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4) !important;
            transform: scale(1.05) !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2 text-white" style="font-size: 2.5rem; font-weight: 700;">
                        <i class="bi bi-graph-up me-3"></i>
                        Payment Analytics
                    </h1>
                    <p class="mb-0 opacity-90">Comprehensive payment tracking and client analytics dashboard</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('payment-reports.export', request()->query()) }}" class="btn export-btn">
                        <i class="bi bi-download"></i>
                        Export Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="filter-section">
            <form method="GET" action="{{ route('payment-reports.index') }}" id="filterForm">
                <!-- Report Type Toggle -->
                <div class="d-flex justify-content-center mb-4">
                    <div class="report-type-toggle">
                        <input type="radio" class="btn-check d-none" name="report_type" id="paid" value="paid"
                            {{ $reportType === 'paid' ? 'checked' : '' }}>
                        <label class="report-type-btn" for="paid" data-value="paid">
                            <i class="bi bi-check-circle me-2"></i>Paid Clients
                        </label>

                        <input type="radio" class="btn-check d-none" name="report_type" id="unpaid" value="unpaid"
                            {{ $reportType === 'unpaid' ? 'checked' : '' }}>
                        <label class="report-type-btn" for="unpaid" data-value="unpaid">
                            <i class="bi bi-exclamation-circle me-2"></i>Unpaid Clients
                        </label>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Month Selection -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="month" class="form-label">Month</label>
                            <select name="month" id="month" class="form-select">
                                @foreach ($months as $month)
                                    <option value="{{ $month }}" {{ $selectedMonth === $month ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Year Selection -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="year" class="form-label">Year</label>
                            <select name="year" id="year" class="form-select">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Search Clients -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search" class="form-label">Search Clients</label>
                            <div class="search-container">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" name="search" id="search" class="form-control search-input"
                                    placeholder="Search by name, ID, or phone..." value="{{ $search }}">
                            </div>
                        </div>
                    </div>

                    <!-- User Search -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="user_search" class="form-label">Collected By</label>
                            <select name="user_search" id="user_search" class="form-select">
                                <option value="">All Users</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ $userSearch == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from" class="form-label">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control"
                                value="{{ $dateFrom }}">
                        </div>
                    </div>

                    <!-- Date To -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to" class="form-label">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control"
                                value="{{ $dateTo }}">
                        </div>
                    </div>
                </div>

                <!-- Filter Button Row -->
                <div class="row g-4 mt-2">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button type="submit" class="btn filter-btn">
                                <i class="bi bi-funnel me-1"></i>Apply Filters
                            </button>
                            <a href="{{ route('payment-reports.index') }}" class="btn btn-secondary ms-2">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stats-card {{ $reportType === 'paid' ? 'paid' : 'unpaid' }}">
                <div class="stats-icon">
                    <i class="bi {{ $reportType === 'paid' ? 'bi-people-fill' : 'bi-person-x' }}"></i>
                </div>
                <div class="stats-value">{{ number_format($data['totalClients']) }}</div>
                <div class="stats-label">{{ $reportType === 'paid' ? 'Paid' : 'Unpaid' }} Clients</div>
            </div>

            @if ($reportType === 'paid')
                <div class="stats-card collection">
                    <div class="stats-icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="stats-value">৳{{ number_format($data['totalAmount'], 2) }}</div>
                    <div class="stats-label">
                        @if ($selectedUser)
                            {{ $selectedUser->name }}'s Total Collection
                        @else
                            Total Collection
                        @endif
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="bi bi-tag"></i>
                    </div>
                    <div class="stats-value">৳{{ number_format($data['totalDiscount'], 2) }}</div>
                    <div class="stats-label">Total Discount</div>
                </div>

                <div class="stats-card collection">
                    <div class="stats-icon">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="stats-value">৳{{ number_format($data['totalCollection'], 2) }}</div>
                    <div class="stats-label">
                        @if ($selectedUser)
                            {{ $selectedUser->name }}'s Gross Revenue
                        @else
                            Gross Revenue
                        @endif
                    </div>
                </div>
            @else
                <div class="stats-card unpaid">
                    <div class="stats-icon">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="stats-value">৳{{ number_format($data['totalDueAmount'], 2) }}</div>
                    <div class="stats-label">Outstanding Amount</div>
                </div>

                <div class="stats-card">
                    <div class="stats-icon">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="stats-value">৳{{ number_format($data['totalBillAmount'], 2) }}</div>
                    <div class="stats-label">Total Bills</div>
                </div>

                <div class="stats-card unpaid">
                    <div class="stats-icon">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div class="stats-value">
                        {{ $data['totalBillAmount'] > 0 ? number_format(($data['totalDueAmount'] / $data['totalBillAmount']) * 100, 1) : 0 }}%
                    </div>
                    <div class="stats-label">Outstanding Ratio</div>
                </div>
            @endif
        </div>

        <!-- Data Table -->
        <div class="data-table">
            <div class="table-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="bi bi-table"></i>
                        {{ $reportType === 'paid' ? 'Paid' : 'Unpaid' }} Clients Report
                        <small class="opacity-75">{{ $selectedMonth }} {{ $selectedYear }}</small>
                    </h5>
                    @if ($data['totalClients'] > 0)
                        <span class="badge bg-light text-dark">{{ $data['totalClients'] }} Records</span>
                    @endif
                </div>
            </div>

            @if ($data['totalClients'] > 0)
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Info</th>
                                <th>Contact</th>
                                <th>Package</th>
                                @if ($reportType === 'paid')
                                    <th>Amount Paid</th>
                                    <th>Discount</th>
                                    <th>Payment Date</th>
                                    <th>Collected By</th>
                                @else
                                    <th>Due Amount</th>
                                    <th>Bill Amount</th>
                                    <th>Status</th>
                                @endif
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['clients'] as $index => $client)
                                @if ($reportType === 'paid')
                                    @foreach ($client->payments as $paymentIndex => $payment)
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-muted">
                                                    {{ $index + 1 }}{{ $paymentIndex > 0 ? '.' . ($paymentIndex + 1) : '' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="client-info">
                                                    <div class="client-avatar">
                                                        {{ strtoupper(substr($client->username, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="client-name">{{ $client->username }}</div>
                                                        <div class="client-id">{{ $client->client_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <i class="bi bi-telephone text-muted me-1"></i>
                                                <span>{{ $client->phone_number }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary bg-opacity-25 text-primary">
                                                    {{ $client->package->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="amount-success">৳{{ number_format($payment->amount, 2) }}</td>
                                            <td>
                                                <span
                                                    class="text-warning fw-bold">৳{{ number_format($payment->discount, 2) }}</span>
                                            </td>
                                            <td>
                                                <i class="bi bi-calendar-event text-muted me-1"></i>
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $payment->collectedBy->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('clients.show', $client->id) }}"
                                                    class="btn action-btn">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="client-info">
                                                <div class="client-avatar" style="background: var(--danger-gradient);">
                                                    {{ strtoupper(substr($client->username, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="client-name">{{ $client->username }}</div>
                                                    <div class="client-id">{{ $client->client_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="bi bi-telephone text-muted me-1"></i>
                                            <span>{{ $client->phone_number }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-25 text-primary">
                                                {{ $client->package->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="amount-danger">৳{{ number_format($client->due_amount, 2) }}</td>
                                        <td>
                                            <span
                                                class="text-muted fw-bold">৳{{ number_format($client->bill_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="status-badge status-{{ $client->status === 'paid' ? 'paid' : 'due' }}">
                                                {{ ucfirst($client->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('clients.show', $client->id) }}" class="btn action-btn">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi {{ $reportType === 'paid' ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
                    </div>
                    <h4>No {{ $reportType }} clients found</h4>
                    <p class="text-muted">
                        No clients match your search criteria for {{ $selectedMonth }} {{ $selectedYear }}.
                        Try adjusting your filters or search terms.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize button states on page load
            updateButtonStates();

            // Handle report type button clicks
            $('.report-type-btn').click(function(e) {
                e.preventDefault();

                const value = $(this).data('value');
                const radioInput = $('#' + value);

                // Uncheck all radio buttons and remove active class
                $('input[name="report_type"]').prop('checked', false);
                $('.report-type-btn').removeClass('active');

                // Check the selected radio button and add active class
                radioInput.prop('checked', true);
                $(this).addClass('active');

                // Submit the form
                $('#filterForm').submit();
            });

            // Auto-submit form when month/year/user_search changes
            $('#month, #year, #user_search').change(function() {
                $('#filterForm').submit();
            });

            // Auto-submit form when date range changes
            $('#date_from, #date_to').change(function() {
                $('#filterForm').submit();
            });

            // Update button states function
            function updateButtonStates() {
                $('.report-type-btn').removeClass('active');
                $('input[name="report_type"]:checked').each(function() {
                    $('label[for="' + this.id + '"]').addClass('active');
                });
            }

            // Real-time search with debounce
            let searchTimeout;
            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    $('#filterForm').submit();
                }, 800);
            });

            // Add loading state to export button
            $('.export-btn').click(function(e) {
                const $btn = $(this);
                const originalContent = $btn.html();

                $btn.html('<i class="bi bi-arrow-clockwise spin"></i> Exporting...');
                $btn.addClass('disabled');

                // Allow the download to proceed
                setTimeout(function() {
                    $btn.html(originalContent);
                    $btn.removeClass('disabled');
                }, 2000);
            });

            // Smooth animations for stats cards
            $('.stats-card').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
                $(this).addClass('animate-in');
            });

            // Add fade-in effect for table rows
            $('.data-table tbody tr').each(function(index) {
                $(this).css('animation-delay', (index * 0.05) + 's');
                $(this).addClass('row-animate-in');
            });

            // Enhanced form validation
            $('#filterForm').on('submit', function() {
                const $submitBtn = $('.filter-btn');
                const originalText = $submitBtn.html();

                $submitBtn.html('<i class="bi bi-arrow-clockwise spin me-1"></i>Loading...');
                $submitBtn.prop('disabled', true);

                // Re-enable button after form submission
                setTimeout(function() {
                    $submitBtn.html(originalText);
                    $submitBtn.prop('disabled', false);
                }, 1000);
            });
        });

        // Add enhanced animations
        document.head.insertAdjacentHTML('beforeend', `
<style>
    .spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-in {
        animation: slideInUp 0.6s ease-out forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    @keyframes slideInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .row-animate-in {
        animation: fadeInLeft 0.4s ease-out forwards;
        opacity: 0;
        transform: translateX(-20px);
    }

    @keyframes fadeInLeft {
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Enhanced focus states */
    .form-control:focus,
    .form-select:focus {
        transform: scale(1.02);
    }

    .report-type-btn {
        position: relative;
        overflow: hidden;
    }

    .report-type-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .report-type-btn:hover::before {
        left: 100%;
    }
</style>
`);
    </script>
@endsection
