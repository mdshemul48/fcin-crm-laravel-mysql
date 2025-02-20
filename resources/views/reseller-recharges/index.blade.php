@extends('layouts.app')

@section('title', 'Reseller Recharges')

@section('header_content')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRechargeModal">
        <i class="bi bi-plus-lg me-1"></i> Add Recharge
    </button>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('reseller-recharges.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Reseller</label>
                        <select name="reseller_id" class="form-select">
                            <option value="">All Resellers</option>
                            @foreach ($resellers as $reseller)
                                <option value="{{ $reseller->id }}"
                                    {{ request('reseller_id') == $reseller->id ? 'selected' : '' }}>
                                    {{ $reseller->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date"
                            value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date"
                            value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-1"></i> Filter Results
                        </button>
                        @if (request()->hasAny(['reseller_id', 'start_date', 'end_date']))
                            <a href="{{ route('reseller-recharges.index') }}" class="btn btn-light w-100 mt-2">Clear
                                Filters</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Total Recharge</h6>
                                <h3 class="mb-0">৳{{ number_format($totals->total_amount ?? 0, 2) }}</h3>
                            </div>
                            <div>
                                <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Total Commission</h6>
                                <h3 class="mb-0">৳{{ number_format($recharges->sum('commission'), 2) }}</h3>
                            </div>
                            <div>
                                <i class="bi bi-percent fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info bg-gradient text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Total Transactions</h6>
                                <h3 class="mb-0">{{ $recharges->count() }}</h3>
                            </div>
                            <div>
                                <i class="bi bi-arrow-repeat fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recharges Table -->
        <div class="card border-0 shadow">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Reseller</th>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Commission</th>
                                <th>Notes</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recharges as $recharge)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($recharge->reseller->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $recharge->reseller->name }}</h6>
                                                <small class="text-muted">{{ $recharge->reseller->phone }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $recharge->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="text-end">৳{{ number_format($recharge->amount, 2) }}</td>
                                    <td class="text-end">৳{{ number_format($recharge->commission, 2) }}</td>
                                    <td>{{ Str::limit($recharge->notes, 30) }}</td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editRechargeModal{{ $recharge->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-light btn-sm"
                                                onclick="confirmDelete('{{ $recharge->id }}')">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-clock-history display-4 text-muted"></i>
                                            <p class="mt-3">No recharges found</p>
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
            {{ $recharges->links() }}
        </div>

        <!-- Add Recharge Modal -->
        <div class="modal fade" id="addRechargeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Recharge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('reseller-recharges.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Reseller</label>
                                <select name="reseller_id" class="form-select" required>
                                    <option value="">Select Reseller</option>
                                    @foreach ($resellers as $reseller)
                                        <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" class="form-control" name="amount" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Commission</label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" class="form-control" name="commission"
                                        required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Recharge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal for each recharge -->
        @foreach ($recharges as $recharge)
            <div class="modal fade" id="editRechargeModal{{ $recharge->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Recharge</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('reseller-recharges.update', $recharge->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Reseller</label>
                                    <select name="reseller_id" class="form-select" required>
                                        @foreach ($resellers as $reseller)
                                            <option value="{{ $reseller->id }}"
                                                {{ $recharge->reseller_id == $reseller->id ? 'selected' : '' }}>
                                                {{ $reseller->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" class="form-control" name="amount"
                                            value="{{ $recharge->amount }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Commission</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" class="form-control" name="commission"
                                            value="{{ $recharge->commission }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="notes" rows="3">{{ $recharge->notes }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Recharge</button>
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
    </div>
@endsection

@section('css')
    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            background-color: var(--bs-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-group .btn {
            padding: 0.375rem 0.5rem;
        }

        .card {
            border-radius: 0.5rem;
        }

        .bg-gradient {
            background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 100%);
        }

        .table th {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this recharge?')) {
                const form = document.getElementById('deleteForm');
                form.action = `/reseller-recharges/${id}`;
                form.submit();
            }
        }
    </script>
@endsection
