<div class="d-block d-md-none">
    @foreach ($clients as $client)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Client #{{ $client->id }}</h5>
                <p class="card-text"><strong>Username:</strong> {{ $client->username }}</p>
                <p class="card-text"><strong>Phone:</strong> {{ $client->phone_number }}</p>
                <p class="card-text"><strong>Client Id:</strong> {{ $client->client_id }}</p>
                <p class="card-text"><strong>Payment Status:</strong>
                    @if ($client->status == 'paid')
                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Paid</span>
                    @else
                        <span class="badge bg-danger rounded-pill"><i
                                class="bi bi-exclamation-circle me-1"></i>Unpaid</span>
                    @endif
                </p>
                <p class="card-text"><strong>Billing Status:</strong>
                    @if ($client->billing_status)
                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-circle me-1"></i>Active</span>
                    @else
                        <span class="badge bg-danger rounded-pill"><i
                                class="bi bi-exclamation-circle me-1"></i>Inactive</span>
                    @endif
                </p>
                <p class="card-text"><strong>Due Amount:</strong> {{ $client->due_amount ?? '0.00' }}</p>
                <p class="card-text"><strong>Package:</strong>
                    <span class="badge bg-primary">{{ $client->package->name }}({{ $client->package->price }})</span>
                    <span class="text-muted">{{ $client->bill_amount }}</span>
                </p>
                <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-info mb-1 mb-md-0">
                    <i class="bi bi-eye"></i> View
                </a>
                <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit
                </a>
            </div>
        </div>
    @endforeach
</div>
