@extends('layouts.app')

@section('title', 'Add Client')

@section('content')
    <div class="container">
        <h1 class="my-3">Add New Client</h1>
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="client_id" class="form-label">Client ID</label>
                <input type="text" name="client_id" id="client_id" class="form-control" value="{{ old('client_id') }}"
                    required>
                @error('client_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}"
                    required>
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control"
                    value="{{ old('phone_number') }}" required>
                @error('phone_number')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                @error('address')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="package_id" class="form-label">Package ID</label>
                <input type="number" name="package_id" id="package_id" class="form-control" value="{{ old('package_id') }}"
                    required>
                @error('package_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="bill_amount" class="form-label">Bill Amount</label>
                <input type="number" step="0.01" name="bill_amount" id="bill_amount" class="form-control"
                    value="{{ old('bill_amount') }}" required>
                @error('bill_amount')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="disabled" class="form-label">Disabled</label>
                <select name="disabled" id="disabled" class="form-control">
                    <option value="0" {{ old('disabled') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('disabled') == '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Create Client</button>
            </div>
        </form>
    </div>
@endsection
