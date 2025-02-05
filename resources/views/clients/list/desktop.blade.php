<div class="d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Phone</th>
                    <th>Client Id</th>
                    <th>Payment Status</th>
                    <th>Account Status</th>
                    <th>Due Amount</th>
                    <th>Package</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    <tr>
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
                        <td>{{ $client->due_amount ?? '0.00' }}</td>
                        <td>
                            <span
                                class="badge bg-primary">{{ $client->package->name }}({{ $client->package->price }})</span>
                            <span class="text-muted">{{ $client->bill_amount }}</span>
                        </td>
                        <td>
                            <a href="{{ route('clients.show', $client->id) }}" class="btn btn-sm btn-info mb-1 mb-md-0">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
