@extends('layouts.app')

@section('title', 'Client Details')

@section('header_content')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
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
                                <li class="pt-2"><strong>Client Id:</strong> {{ $client->client_id }}</li>
                                <li class="py-2 border-bottom">
                                    <strong>Package:</strong>
                                    <span class="badge bg-primary">{{ $client->package->name }}</span>
                                    <span class="text-muted">({{ $client->package->price }})</span>
                                </li>
                                <li class="py-2 border-bottom"><strong>Bill Amount:</strong> {{ $client->bill_amount }}</li>
                                <li class="py-2">
                                    <strong>Status:</strong>
                                    @if ($client->status == 'paid')
                                        <span class="badge bg-success rounded-pill"><i
                                                class="bi bi-check-circle me-1"></i>Paid</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill"><i
                                                class="bi bi-exclamation-circle me-1"></i>Unpaid</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="balance-card custom-bg-primary text-white rounded-4 p-4 shadow-lg hover-scale">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper bg-white bg-opacity-10 p-3 rounded-3 me-3">
                                            <i class="bi bi-clock-history fs-1"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Due Balance</h5>
                                            <div class="d-flex align-items-baseline">
                                                <span class="h2 fw-bold me-2">{{ $client->due_balance ?? '0.00' }}</span>
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

                </div>
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
                transform: translateX(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) rotate(45deg);
            }
        }

        .remarks-content {
            min-height: 100px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
