@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Expense</h5>
                        <a href="{{ route('expenses.index') }}" class="btn btn-light btn-sm">Back to List</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('expenses.update', $expense->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    value="{{ old('title', $expense->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                        value="{{ old('amount', $expense->amount) }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="expense_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="expense_date" name="expense_date"
                                    value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $expense->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="receipt_image" class="form-label">Receipt Image</label>
                                <input type="file" class="form-control" id="receipt_image" name="receipt_image"
                                    accept="image/*">
                                @if ($expense->receipt_image)
                                    <div class="mt-2">
                                        <small class="text-muted">Current Receipt:</small>
                                        <button type="button" class="btn btn-sm btn-info ms-2"
                                            onclick="showReceipt('{{ Storage::url($expense->receipt_image) }}')">
                                            <i class="fas fa-eye"></i> View Current Receipt
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Update Expense</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.receipt-modal')
@endsection
