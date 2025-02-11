@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('clients.update', $client->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client ID</label>
                            <input type="text" name="client_id" id="client_id" class="form-control"
                                value="{{ old('client_id', $client->client_id) }}" required>
                            @error('client_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control"
                                value="{{ old('username', $client->username) }}" required>
                            @error('username')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control"
                                value="{{ old('phone_number', $client->phone_number) }}" required>
                            @error('phone_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address', $client->address) }}</textarea>
                            @error('address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="package_id" class="form-label">Package</label>
                            <select name="package_id" id="package_id" class="form-control" onchange="updateBillAmount()">
                                <option value="">Select a Package</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}" data-price="{{ $package->price }}"
                                        {{ old('package_id', $client->package_id) == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }} ({{ number_format($package->price, 2) }}৳)
                                    </option>
                                @endforeach
                            </select>
                            @error('package_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="bill_amount" class="form-label">Bill Amount</label>
                            <input type="number" step="0.01" name="bill_amount" id="bill_amount" class="form-control"
                                value="{{ old('bill_amount', $client->bill_amount) }}" required>
                            @error('bill_amount')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="billing_status" class="form-label">Billing Status</label>
                            <select name="billing_status" id="billing_status" class="form-control">
                                <option value="1" {{ old('billing_status', $client->billing_status) == '1' ? 'selected' : '' }}>
                                    Enable
                                </option>
                                <option value="0" {{ old('billing_status', $client->billing_status) == '0' ? 'selected' : '' }}>
                                    disable
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control">{{ old('remarks', $client->remarks) }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Client</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('custom-scripts')
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
    @endpush

    @section('css')
        <style>
            :root {
                --primary-color: #00B4B4;
                --primary-hover: #009999;
                --primary-light: rgba(0, 180, 180, 0.1);
            }

            .form-control:focus, 
            .form-select:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(0, 180, 180, 0.25);
            }

            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }

            .btn-primary:hover {
                background-color: var(--primary-hover);
                border-color: var(--primary-hover);
            }

            /* Add your custom styles here */
        </style>
    @endsection
@endsection
