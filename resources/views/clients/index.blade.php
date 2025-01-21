@extends('layouts.app')

@section('title', 'Clients List')

@section('header_content')
    <div class="d-flex flex-column flex-md-row justify-content-between">
        <div class="mb-2 mb-md-0">
            <form action="{{ route('clients.index') }}" method="GET" class="d-flex flex-column flex-md-row">
                <input type="text" name="search" class="form-control me-0 me-md-2 mb-2 mb-md-0"
                    placeholder="Search by Username, Number, or C.ID" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="ms-0 ms-md-1"><a href="{{ route('clients.create') }}" class="btn btn-primary">Add New Client</a></div>
    </div>
@endsection

@section('content')
    <div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client ID</th>
                        <th>Username</th>
                        <th>Phone Number</th>
                        <th>Package</th>
                        <th>Bill Amount</th>
                        <th>Disabled</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->client_id }}</td>
                            <td>{{ $client->username }}</td>
                            <td>{{ $client->phone_number }}</td>
                            <td> {{ $client->package->name }} ({{ number_format($client->package->price, 2) }} ৳)</td>
                            <td>{{ $client->bill_amount }}</td>
                            <td>{{ $client->disabled ? 'Yes' : 'No' }}</td>
                            <td>{{ $client->createdBy->name ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('clients.show', $client->id) }}" class="btn btn-info btn-sm">Details</a>
                                <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $clients->links() }}
    </div>
@endsection
