@extends('layouts.app')

@section('title', 'Clients List')

@section('content')
    <div class="container-fluid">
        <a href="{{ route('clients.create') }}" class="btn btn-primary mb-3 mt-1">Add New Client</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client ID</th>
                    <th>Username</th>
                    <th>Phone Number</th>
                    <th>Package ID</th>
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
                        <td>{{ $client->package_id }}</td>
                        <td>{{ $client->bill_amount }}</td>
                        <td>{{ $client->disabled ? 'Yes' : 'No' }}</td>
                        <td>{{ $client->creator->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('clients.show', $client->id) }}" class="btn btn-info btn-sm">Details</a>
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $clients->links() }}
    </div>
@endsection