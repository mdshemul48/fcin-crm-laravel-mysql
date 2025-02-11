@extends('layouts.app')

@section('title', 'Add Client')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="client_id" id="client_id" 
                                    class="form-control @error('client_id') is-invalid @enderror" 
                                    value="{{ old('client_id') }}" placeholder="Client ID" required>
                                <label for="client_id">
                                    <i class="bi bi-person-badge me-1"></i>Client ID
                                </label>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="username" id="username" 
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username') }}" placeholder="Username" required>
                                <label for="username">
                                    <i class="bi bi-person me-1"></i>Username
                                </label>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="phone_number" id="phone_number" 
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    value="{{ old('phone_number') }}" placeholder="Phone Number" required>
                                <label for="phone_number">
                                    <i class="bi bi-telephone me-1"></i>Phone Number
                                </label>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="package_id" id="package_id" 
                                    class="form-select @error('package_id') is-invalid @enderror" 
                                    onchange="updateBillAmount()">
                                    <option value="">Select a Package</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}" data-price="{{ $package->price }}"
                                            {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }} ({{ number_format($package->price, 2) }} ৳)
                                        </option>
                                    @endforeach
                                </select>
                                <label for="package_id">
                                    <i class="bi bi-box me-1"></i>Package
                                </label>
                                @error('package_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" name="bill_amount" id="bill_amount" 
                                    class="form-control @error('bill_amount') is-invalid @enderror"
                                    value="{{ old('bill_amount') }}" placeholder="Bill Amount" required>
                                <label for="bill_amount">
                                    <i class="bi bi-cash me-1"></i>Bill Amount
                                </label>
                                @error('bill_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="billing_status" id="billing_status" 
                                    class="form-select @error('billing_status') is-invalid @enderror">
                                    <option value="1" {{ old('billing_status') == '1' ? 'selected' : '' }}>Enable</option>
                                    <option value="0" {{ old('billing_status') == '0' ? 'selected' : '' }}>Disable</option>
                                </select>
                                <label for="billing_status">
                                    <i class="bi bi-toggle-on me-1"></i>Billing Status
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="address" id="address" 
                                    class="form-control @error('address') is-invalid @enderror" 
                                    placeholder="Address" rows="3" required>{{ old('address') }}</textarea>
                                <label for="address">
                                    <i class="bi bi-geo-alt me-1"></i>Address
                                </label>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="remarks" id="remarks" 
                                    class="form-control @error('remarks') is-invalid @enderror" 
                                    placeholder="Remarks">{{ old('remarks') }}</textarea>
                                <label for="remarks">
                                    <i class="bi bi-chat-left-text me-1"></i>Remarks
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Add Client
                            </button>
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

                // Get the selected package price from the data attribute
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
            .form-floating {
                position: relative;
            }

            .form-control, .form-select {
                height: 3.5rem;
                border: 1px solid rgba(0,0,0,0.1);
                border-radius: 8px;
            }

            .form-control:focus, .form-select:focus {
                border-color: #3498db;
                box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            }

            .form-floating label {
                padding-left: 1rem;
                color: #6c757d;
            }

            .form-floating > .form-control:focus ~ label,
            .form-floating > .form-control:not(:placeholder-shown) ~ label {
                color: #3498db;
            }

            .btn-primary {
                padding: 0.8rem 2rem;
                font-weight: 500;
            }
        </style>
    @endsection
@endsection
