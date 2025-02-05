<div class="col-12">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="bg-light p-4 rounded-3 shadow-sm">
                <h5 class="text-primary mb-3"><i class="bi bi-cash-stack me-2"></i>Payments</h5>
                <ul class="list-group list-group-flush">
                    @php
                        $totalAmount = 0;
                        $totalDiscount = 0;
                    @endphp
                    @forelse ($client->payments->sortByDesc('created_at') as $payment)
                        @php
                            $totalAmount += $payment->amount;
                            $totalDiscount += $payment->discount;
                        @endphp
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fw-bold"><i
                                                class="bi bi-currency-dollar me-1"></i>{{ $payment->amount }}
                                            Taka</div>
                                        <small class="text-muted"><i
                                                class="bi bi-wallet2 me-1"></i>{{ $payment->amount_from_client_account }}
                                            Taka</small><br>
                                        @if ($payment->discount != 0)
                                            <small class="text-danger"><i
                                                    class="bi bi-dash-circle me-1"></i>{{ $payment->discount }}
                                                Taka</small><br>
                                        @endif
                                        <small class="text-muted"><i
                                                class="bi bi-calendar me-1"></i>{{ $payment->payment_date }}</small><br>
                                        <small class="text-muted"><i
                                                class="bi bi-tag me-1"></i>{{ ucfirst($payment->payment_type) }} -
                                            {{ $payment->month }}</small><br>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted"><i
                                                class="bi bi-clock me-1"></i>{{ $payment->created_at }}</small><br>
                                        @if ($payment->collectedBy->id === $payment->createdBy->id)
                                            <small class="text-muted"><i
                                                    class="bi bi-person me-1"></i>{{ $payment->collectedBy->name ?? 'N/A' }}</small>
                                        @else
                                            <small class="text-muted"><i
                                                    class="bi bi-person-check me-1"></i>{{ $payment->collectedBy->name ?? 'N/A' }}</small><br>
                                            <small class="text-muted"><i
                                                    class="bi bi-person-plus me-1"></i>{{ $payment->createdBy->name ?? 'N/A' }}</small>
                                        @endif
                                        @if ($payment->remarks)
                                            <br>
                                            <small class="text-muted"><i
                                                    class="bi bi-chat-left-text me-1"></i>{{ $payment->remarks }}</small>
                                        @endif
                                    </div>
                                    @if (canAccess('admin'))
                                        <div class="col-md-12 text-end">
                                            <form action="" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this payment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <li class="list-group-item text-center text-muted">No payments found</li>
                    @endforelse
                </ul>
                <div class="mt-3 text-end">
                    <strong>Total Amount:</strong> {{ $totalAmount }} Taka<br>
                    <strong>Total Discount:</strong> {{ $totalDiscount }} Taka
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light p-4 rounded-3 shadow-sm">
                <h5 class="text-primary mb-3"><i class="bi bi-receipt me-2"></i>Generated Bills</h5>
                <ul class="list-group list-group-flush">
                    @php
                        $totalBillsAmount = 0;
                    @endphp
                    @forelse ($client->generatedBills as $bill)
                        @php
                            $totalBillsAmount += $bill->amount;
                        @endphp
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fw-bold"><i
                                                class="bi bi-currency-dollar me-1"></i>{{ $bill->amount }}
                                            Taka</div>
                                        <small class="text-muted"><i
                                                class="bi bi-calendar me-1"></i>{{ $bill->generated_date }}</small><br>
                                        <small class="text-muted"><i
                                                class="bi bi-tag me-1"></i>{{ ucfirst($bill->bill_type) }}</small><br>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted"><i
                                                class="bi bi-clock me-1"></i>{{ $payment->created_at }}</small><br>
                                        <small class="text-muted"><i
                                                class="bi bi-person me-1"></i>{{ $bill->createdBy->name ?? 'N/A' }}</small><br>

                                        @if ($bill->remarks)
                                            <small class="text-muted"><i
                                                    class="bi bi-chat-left-text me-1"></i>{{ $bill->remarks }}</small>
                                        @endif
                                    </div>
                                    @if (canAccess('admin'))
                                        <div class="col-md-12 text-end mt-2">
                                            <form action="" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this bill?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <li class="list-group-item text-center text-muted">No generated bills found
                        </li>
                    @endforelse
                </ul>
                <div class="mt-3 text-end">
                    <strong>Total Bills Amount:</strong> {{ $totalBillsAmount }} Taka
                </div>
            </div>
        </div>
    </div>
</div>
