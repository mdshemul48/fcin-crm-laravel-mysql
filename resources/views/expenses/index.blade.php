@extends('layouts.app')

@section('title', 'Expense Management')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Add New Expense</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                        required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="expense_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="receipt_image" class="form-label">Receipt Image</label>
                                <input type="file" class="form-control" id="receipt_image" name="receipt_image"
                                    accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Expense</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Expense List</h5>
                            <div class="text-end">
                                <strong>Total:</strong>
                                <span class="badge bg-success fs-6">৳{{ number_format($totalExpense, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="row g-3 mb-4">
                            <div class="col-md-4">
                                <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Title</th>
                                        <th>Amount</th>
                                        <th>Created By</th>
                                        <th>Receipt</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                            <td>
                                                <strong>{{ $expense->title }}</strong>
                                                @if ($expense->description)
                                                    <small class="d-block text-muted">{{ $expense->description }}</small>
                                                @endif
                                            </td>
                                            <td>৳{{ number_format($expense->amount, 2) }}</td>
                                            <td>{{ $expense->createdBy->name }}</td>
                                            <td>
                                                @if ($expense->receipt_image)
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        onclick="showReceipt('{{ Storage::url($expense->receipt_image) }}')">
                                                        <i class="fas fa-eye"></i> View
                                                    </button>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('expenses.edit', $expense->id) }}"
                                                        class="btn btn-sm btn-primary me-1">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ route('expenses.destroy', $expense->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this expense?')">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
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

    @include('components.receipt-modal')
@endsection

@section('css')
    <style>
        .card {
            border: none;
            margin-bottom: 1.5rem;
        }

        .table th {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 0.5em 1em;
        }
    </style>
@endsection
