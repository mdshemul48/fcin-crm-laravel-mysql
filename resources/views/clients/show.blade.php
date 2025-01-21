@extends('layouts.app')

@section('title', 'Client Details')
@section('header_content')
    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
@section('content')
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>ID:</strong> {{ $client->id }}</li>
                        <li class="list-group-item"><strong>Client ID:</strong> {{ $client->client_id }}</li>
                        <li class="list-group-item"><strong>Username:</strong> {{ $client->username }}</li>
                        <li class="list-group-item"><strong>Phone Number:</strong> {{ $client->phone_number }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Package:</strong> {{ $client->package->name }}
                            ({{ $client->package->price }})</li>
                        <li class="list-group-item"><strong>Bill Amount:</strong> {{ $client->bill_amount }}</li>
                        <li class="list-group-item">
                            <strong>Billing Disabled:</strong>
                            <span
                                class="{{ $client->disabled ? 'bg-danger text-light rounded p-1' : 'bg-success text-light rounded p-1' }}">
                                {{ $client->disabled ? 'YES' : 'NO' }}
                            </span>
                        </li>
                        <li class="list-group-item"><strong>Created By:</strong> {{ $client->createdBy->name ?? 'N/A' }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="form-group mt-3">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" class="form-control" readonly>{{ $client->remarks }}</textarea>
            </div>
        </div>

    </div>
@endsection
