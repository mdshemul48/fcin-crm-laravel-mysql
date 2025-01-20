@extends('layouts.app')

@section('title', 'Client Details')
@section('header_content')
    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
@section('content')

    <ul class="list-group">
        <li class="list-group-item"><strong>ID:</strong> {{ $client->id }}</li>
        <li class="list-group-item"><strong>Client ID:</strong> {{ $client->client_id }}</li>
        <li class="list-group-item"><strong>Username:</strong> {{ $client->username }}</li>
        <li class="list-group-item"><strong>Phone Number:</strong> {{ $client->phone_number }}</li>
        <li class="list-group-item"><strong>Package ID:</strong> {{ $client->package->name }}</li>
        <li class="list-group-item"><strong>Bill Amount:</strong> {{ $client->bill_amount }}</li>
        <li class="list-group-item"><strong>Disabled:</strong> {{ $client->disabled ? 'Yes' : 'No' }}</li>
        <li class="list-group-item"><strong>Created By:</strong> {{ $client->creator->name ?? 'N/A' }}</li>
    </ul>
@endsection
