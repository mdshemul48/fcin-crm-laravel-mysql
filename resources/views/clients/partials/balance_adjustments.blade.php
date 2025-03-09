<div class="col-12 mt-4">
    <div class="bg-light p-4 rounded-3 shadow-sm">
        <h5 class="text-primary mb-3"><i class="bi bi-sliders me-2"></i>Balance Adjustment History</h5>
        <ul class="list-group list-group-flush">
            @forelse ($balanceAdjustments as $adjustment)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fw-bold">
                                    <i class="bi bi-gear-fill me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $adjustment->adjustment_type)) }} Adjustment
                                </div>
                                
                                @if($adjustment->adjustment_type == 'current_balance' || $adjustment->adjustment_type == 'both')
                                    <div class="d-flex align-items-center mt-2">
                                        <span class="badge bg-secondary me-2">Current Balance</span>
                                        <span class="text-muted me-2">{{ $adjustment->old_current_balance }}</span>
                                        <i class="bi bi-arrow-right text-primary mx-2"></i>
                                        <span class="text-success fw-bold">{{ $adjustment->new_current_balance }}</span>
                                    </div>
                                @endif
                                
                                @if($adjustment->adjustment_type == 'due_amount' || $adjustment->adjustment_type == 'both')
                                    <div class="d-flex align-items-center mt-2">
                                        <span class="badge bg-secondary me-2">Due Amount</span>
                                        <span class="text-muted me-2">{{ $adjustment->old_due_amount }}</span>
                                        <i class="bi bi-arrow-right text-primary mx-2"></i>
                                        <span class="text-danger fw-bold">{{ $adjustment->new_due_amount }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>Adjusted by: {{ $adjustment->adjustedBy->name ?? 'N/A' }}
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $adjustment->created_at->format('M d, Y h:i A') }}
                                </small>
                                
                                @if ($adjustment->remarks)
                                    <div class="mt-2 p-2 bg-light rounded">
                                        <small class="text-muted">
                                            <i class="bi bi-chat-left-text me-1"></i>{{ $adjustment->remarks }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <li class="list-group-item text-center text-muted">No balance adjustments found</li>
            @endforelse
        </ul>
    </div>
</div> 