@extends('layouts.app')

@section('title', 'Resellers')

@section('header_content')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResellerModal">
        <i class="bi bi-plus-lg me-1"></i> Add Reseller
    </button>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Name</th>
                                <th>Location</th>
                                <th class="text-end">This Month Recharge</th>
                                <th class="text-end">This Month Commission</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resellers as $reseller)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($reseller->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $reseller->name }}</h6>
                                                <small class="text-muted">{{ $reseller->phone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $reseller->location ?? 'N/A' }}</td>
                                    <td class="text-end">৳{{ number_format($reseller->current_month_recharges, 2) }}</td>
                                    <td class="text-end">৳{{ number_format($reseller->current_month_commission, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editResellerModal{{ $reseller->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-people display-4 text-muted"></i>
                                            <p class="mt-3">No resellers found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            {{ $resellers->links() }}
        </div>
    </div>

    <!-- Add Reseller Modal -->
    <div class="modal fade" id="addResellerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Reseller</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('resellers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Reseller</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($resellers as $reseller)
        <!-- Edit Reseller Modal -->
        <div class="modal fade" id="editResellerModal{{ $reseller->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Reseller</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('resellers.update', $reseller->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $reseller->name }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" value="{{ $reseller->phone }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location"
                                    value="{{ $reseller->location }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3">{{ $reseller->notes }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Reseller</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('css')
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #495057;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }

        .table th {
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-group .btn {
            padding: 0.375rem 0.5rem;
        }

        .modal-content {
            border: none;
            border-radius: 0.5rem;
        }

        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, .05);
            background-color: #f8f9fa;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
    </style>
@endsection
