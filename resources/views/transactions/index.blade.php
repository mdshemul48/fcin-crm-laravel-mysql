@extends('layouts.app')

@section('title', 'Money Transfer')

@section('header_content')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferModal">
        <i class="bi bi-send-fill me-1"></i> Record Transaction
    </button>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Date Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('transactions.index') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date"
                            value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        @if (request()->hasAny(['start_date', 'end_date']))
                            <a href="{{ route('transactions.index') }}" class="btn btn-light w-100 mt-2">
                                Reset to Current Month
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- My Transaction Summary -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-success bg-gradient text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-uppercase mb-1">
                                    Total Received ({{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }})
                                </h6>
                                <h2 class="mb-0">৳{{ number_format($myTotalReceived, 2) }}</h2>
                            </div>
                            <div><i class="bi bi-arrow-down-circle fs-1 opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm bg-primary bg-gradient text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-uppercase mb-1">My Total Given</h6>
                                <h2 class="mb-0">৳{{ number_format($myTotalSent, 2) }}</h2>
                            </div>
                            <div><i class="bi bi-arrow-up-circle fs-1 opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card border-0 shadow">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Transactions</h5>
                <div class="small text-muted">Your transactions are highlighted</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Note</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                @php
                                    $isMyTransaction =
                                        $transaction->from_user_id === auth()->id() ||
                                        $transaction->to_user_id === auth()->id();
                                    $canEdit = $transaction->from_user_id === auth()->id();
                                @endphp
                                <tr class="{{ $isMyTransaction ? 'table-light' : '' }}">
                                    <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="user-icon me-2 {{ $transaction->from_user_id === auth()->id() ? 'bg-primary text-white' : '' }}">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <span>{{ $transaction->fromUser->name }}</span>
                                            @if ($transaction->from_user_id === auth()->id())
                                                <span class="badge bg-primary ms-2">You</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div
                                                class="user-icon me-2 {{ $transaction->to_user_id === auth()->id() ? 'bg-success text-white' : '' }}">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <span>{{ $transaction->toUser->name }}</span>
                                            @if ($transaction->to_user_id === auth()->id())
                                                <span class="badge bg-success ms-2">You</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $transaction->note ?? 'N/A' }}</td>
                                    <td class="text-end">৳{{ number_format($transaction->amount, 2) }}</td>
                                    <td class="text-end">
                                        @if ($canEdit)
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $transaction->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light text-danger"
                                                    onclick="confirmDelete('{{ $transaction->id }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select User</label>
                            <select name="to_user_id" class="form-select" required>
                                <option value="">Choose user...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
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
                            <label class="form-label">Note (Optional)</label>
                            <textarea class="form-control" name="note" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Edit Modals -->
    @foreach ($transactions as $transaction)
        @if ($transaction->from_user_id === auth()->id())
            <div class="modal fade" id="editModal{{ $transaction->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Transaction</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Select User</label>
                                    <select name="to_user_id" class="form-select" required>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $transaction->to_user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" class="form-control" name="amount"
                                            value="{{ $transaction->amount }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Note (Optional)</label>
                                    <textarea class="form-control" name="note" rows="2">{{ $transaction->note }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Record</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('css')
    <style>
        .badge {
            padding: 0.5em 0.75em;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }

        .user-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #f8f9fa;
        }

        .table-light {
            background-color: rgba(67, 97, 238, 0.05);
        }

        .input-group-text {
            border-radius: 0.375rem;
        }
    </style>
@endsection

@section('scripts')
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this transaction?')) {
                const form = document.getElementById('deleteForm');
                form.action = `/transactions/${id}`;
                form.submit();
            }
        }
    </script>
@endsection
