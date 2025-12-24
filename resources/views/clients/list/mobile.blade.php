<div class="d-block d-md-none">
    <div class="mb-3">
        <input type="checkbox" id="selectAllMobile" class="form-check-input">
        <label for="selectAllMobile" class="form-check-label ms-2">Select All</label>
    </div>
    @foreach ($clients as $client)
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input client-checkbox" name="selected_clients[]" value="{{ $client->id }}" id="client-{{ $client->id }}">
                    <label class="form-check-label" for="client-{{ $client->id }}">
                        <h5 class="card-title mb-0">Client #{{ $client->id }}</h5>
                    </label>
                </div>
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
