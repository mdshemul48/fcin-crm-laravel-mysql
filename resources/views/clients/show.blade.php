@extends('layouts.app')

@section('title', 'Client Details')

@section('header_content')
    <div class="d-flex justify-content-between align-items-center">
        <div class="button-group">
            <a href="{{ route('clients.index') }}" class="btn btn-back">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-edit">
                <i class="bi bi-pencil-square me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#addCustomBillModal">
                <i class="bi bi-file-earmark-plus me-1"></i> Add Custom Bill
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="bi bi-plus-lg me-1"></i> Add Payment
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-card bg-light p-3 rounded-3">
                            <h5 class="text-primary mb-3"><i class="bi bi-person-badge me-2"></i>Basic Information</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="py-2 border-bottom"><strong>ID:</strong> #{{ $client->id }}</li>
                                <li class="py-2 border-bottom"><strong>Username:</strong> {{ $client->username }}</li>
                                <li class="py-2 border-bottom"><strong>Phone:</strong> {{ $client->phone_number }}</li>
                                <li class="pt-2"><strong>Address:</strong> {{ $client->address }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card bg-light p-3 rounded-3">
                            <h5 class="text-primary mb-3"><i class="bi bi-credit-card me-2"></i>Billing Details</h5>
                            <ul class="list-unstyled mb-0">
                                <li class="pt-2 border-bottom"><strong>Client Id:</strong> {{ $client->client_id }}</li>
                                <li class="py-2 border-bottom">
                                    <strong>Package:</strong>
                                    <span class="badge bg-primary">{{ $client->package->name }}</span>
                                    <span class="text-muted">({{ $client->package->price }})</span>
                                </li>
                                <li class="py-2 border-bottom"><strong>Bill Amount:</strong> {{ $client->bill_amount }}</li>
                                <li class="py-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Payment Status:</strong>
                                            @if ($client->status == 'paid')
                                                <span class="badge bg-success rounded-pill"><i
                                                        class="bi bi-check-circle me-1"></i>Paid</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill"><i
                                                        class="bi bi-exclamation-circle me-1"></i>Unpaid</span>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Account Status:</strong>
                                            @if ($client->billing_status)
                                                <span class="badge bg-success rounded-pill"><i
                                                        class="bi bi-check-circle me-1"></i>Active</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill"><i
                                                        class="bi bi-exclamation-circle me-1"></i>Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="balance-card custom-bg-danger text-white rounded-4 p-4 shadow-lg hover-scale">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper bg-white bg-opacity-10 p-3 rounded-3 me-3">
                                            <i class="bi bi-clock-history fs-1"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Due Amount</h5>
                                            <div class="d-flex align-items-baseline">
                                                <span class="h2 fw-bold me-2">{{ $client->due_amount ?? '0.00' }}</span>
                                                <small class="opacity-75">Taka</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="balance-card custom-bg-success text-white rounded-4 p-4 shadow-lg hover-scale">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper bg-white bg-opacity-10 p-3 rounded-3 me-3">
                                            <i class="bi bi-wallet2 fs-1"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Current Balance</h5>
                                            <div class="d-flex align-items-baseline">
                                                <span
                                                    class="h2 fw-bold me-2">{{ $client->current_balance ?? '0.00' }}</span>
                                                <small class="opacity-75">Taka</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="bg-light p-3 rounded-3">
                            <h5 class="text-primary mb-3"><i class="bi bi-chat-left-text me-2"></i>Remarks</h5>
                            <div class="remarks-content bg-white p-3 rounded-2">
                                {{ $client->remarks ?: 'No remarks available' }}
                            </div>
                        </div>
                    </div>

                    @include('clients.showPageBillPay')

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('payments.store', $client->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="collected_by_id" class="form-label">Collected By</label>
                            <select class="form-select" id="collected_by_id" name="collected_by_id"
                                @if (!canAccess('admin')) disabled @endif>
                                <option value="" selected disabled>Select Collector</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if ($user->id == auth()->id()) selected @endif>
                                        {{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount</label>
                            <input type="number" class="form-control" id="discount" name="discount" value="0">
                        </div>
                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_type" class="form-label">Payment Type</label>
                            <select class="form-select" id="payment_type" name="payment_type">
                                <option value="monthly">Monthly</option>
                                <option value="one_time">One Time</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="month" class="form-label">Month (If Monthly)</label>
                            <select class="form-select" id="month" name="month">
                                <option value="" selected disabled>Select Month</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ date('F', mktime(0, 0, 0, $month, 1)) }}">
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="addCustomBillModal" tabindex="-1" aria-labelledby="addCustomBillModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomBillModalLabel">Add Custom Bill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('bills.generate', $client->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bill_type" class="form-label">Bill Type</label>
                            <select class="form-select" id="bill_type" name="bill_type" required>
                                <option value="" selected disabled>Select Bill Type</option>
                                <option value="monthly">Monthly</option>
                                <option value="one_time">One Time</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="month" class="form-label">Month (If Monthly)</label>
                            <select class="form-select" id="month" name="month">
                                <option value="" selected disabled>Select Month</option>
                                @foreach (range(1, 12) as $month)
                                    <option value="{{ date('F', mktime(0, 0, 0, $month, 1)) }}">
                                        {{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="mb-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Generate Bill</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('css')
    <style>
        .balance-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .custom-bg-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .custom-bg-success {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        .custom-bg-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            transform: rotate(45deg);
            animation: shine 30s infinite;
        }

        .hover-scale:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        }

        .icon-wrapper {
            backdrop-filter: blur(5px);
        }

        @keyframes shine {
            0% {
                transform: rotate(45deg) translateX(0) translateY(0);
            }

            50% {
                transform: rotate(45deg) translateX(50%) translateY(50%);
            }

            100% {
                transform: rotate(45deg) translateX(0) translateY(0);
            }
        }

        .remarks-content {
            font-size: 14px;
            color: #6c757d;
        }

        .list-group-item {
            padding: 1.5rem 1rem;
            border: 1px solid #e9ecef;
            border-radius: .375rem;
            background-color: #f8f9fa;
        }

        .list-group-item strong {
            color: #343a40;
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        .btn-warning {
            background-color: #f0ad4e;
            border-color: #f0ad4e;
            color: white;
        }

        .btn-warning:hover {
            background-color: #ec971f;
            border-color: #d58512;
        }

        .badge.bg-primary {
            background-color: #007bff;
        }

        .badge.bg-success {
            background-color: #28a745;
        }

        .badge.bg-danger {
            background-color: #dc3545;
        }

        .hover-scale:hover {
            transform: scale(1.05);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
        }
    </style>
    <style>
        .button-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            letter-spacing: 0.3px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-back {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .btn-back:hover {
            background: #e9ecef;
            border-color: #ced4da;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-back:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .btn-edit {
            background: linear-gradient(135deg, #ffc107 0%, #ffdb58 100%);
            color: #000;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #e0a800 0%, #f5c516 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.25);
        }

        .btn-edit:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(255, 193, 7, 0.2);
        }

        .btn-custom {
            background: linear-gradient(135deg, #209CEE 0%, #68B8F8 100%);
            color: white;
        }

        .btn-custom:hover {
            background: linear-gradient(135deg, #1A8CD8 0%, #5CA9E2 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(50, 152, 220, 0.25);
        }

        .btn-custom:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(50, 152, 220, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745 0%, #4caf50 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #218838 0%, #43a047 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.25);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(40, 167, 69, 0.2);
        }

        .btn .bi {
            font-size: 0.875rem;
            vertical-align: middle;
        }
    </style>
@endsection
