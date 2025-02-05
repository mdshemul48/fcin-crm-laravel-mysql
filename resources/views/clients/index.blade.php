@extends('layouts.app')

@section('title', 'Clients List')

@section('header_content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
        <form action="{{ route('clients.index') }}" method="GET" class="d-flex ms-3 mb-2 mb-md-0">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Username, Number, or C.ID"
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
        <a href="{{ route('clients.create') }}" class="btn btn-primary ms-1">
            <i class="bi bi-plus-lg me-1"></i> Add Client
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-1">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Actions</th>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Phone</th>
                                <th>Client Id</th>
                                <th>Payment Status</th>
                                <th>Account Status</th>
                                <th>Due Amount</th>
                                <th>Package</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td>
                                        <a href="{{ route('clients.show', $client->id) }}"
                                            class="btn btn-sm btn-info mb-1 mb-md-0">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                    </td>
                                    <td>#{{ $client->id }}</td>
                                    <td>{{ $client->username }}</td>
                                    <td>{{ $client->phone_number }}</td>
                                    <td>{{ $client->client_id }}</td>
                                    <td>
                                        @if ($client->status == 'paid')
                                            <span class="badge bg-success rounded-pill"><i
                                                    class="bi bi-check-circle me-1"></i>Paid</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill"><i
                                                    class="bi bi-exclamation-circle me-1"></i>Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($client->billing_status)
                                            <span class="badge bg-success rounded-pill"><i
                                                    class="bi bi-check-circle me-1"></i>Active</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill"><i
                                                    class="bi bi-exclamation-circle me-1"></i>Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $client->due_amount ?? '0.00' }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary">{{ $client->package->name }}({{ $client->package->price }})</span>
                                        <span class="text-muted">
                                            {{ $client->bill_amount }}
                                        </span>
                                    </td>
                                    <td></td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: black;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
    </style>
@endsection
