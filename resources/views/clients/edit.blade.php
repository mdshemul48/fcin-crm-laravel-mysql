@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
    <div class="container">
        <form action="{{ route('clients.update', $client->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control"
                    value="{{ old('username', $client->username) }}" required>
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control"
                    value="{{ old('phone_number', $client->phone_number) }}" required>
                @error('phone_number')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address', $client->address) }}</textarea>
                @error('address')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="package_id" class="form-label">Package</label>
                <select name="package_id" id="package_id" class="form-control" onchange="updateBillAmount()">
                    <option value="">Select a Package</option>
                    @foreach ($packages as $package)
                        <option value="{{ $package->id }}" data-price="{{ $package->price }}"
                            {{ old('package_id', $client->package_id) == $package->id ? 'selected' : '' }}>
                            {{ $package->name }} ({{ number_format($package->price, 2) }} USD)
                        </option>
                    @endforeach
                </select>
                @error('package_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="bill_amount" class="form-label">Bill Amount</label>
                <input type="number" step="0.01" name="bill_amount" id="bill_amount" class="form-control"
                    value="{{ old('bill_amount', $client->bill_amount) }}" required>
                @error('bill_amount')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="disabled" class="form-label">Disabled</label>
                <select name="disabled" id="disabled" class="form-control">
                    <option value="0" {{ old('disabled', $client->disabled) == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('disabled', $client->disabled) == '1' ? 'selected' : '' }}>Yes</option>
                </select>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Update Client</button>
            </div>
        </form>
    </div>

    <script>
        function updateBillAmount() {
            const packageSelect = document.getElementById('package_id');
            const billAmountInput = document.getElementById('bill_amount');

            const selectedOption = packageSelect.options[packageSelect.selectedIndex];
            const packagePrice = selectedOption.getAttribute('data-price');

            if (packagePrice) {
                billAmountInput.value = parseFloat(packagePrice).toFixed(2);
            } else {
                billAmountInput.value = '';
            }
        }
    </script>
@endsection
