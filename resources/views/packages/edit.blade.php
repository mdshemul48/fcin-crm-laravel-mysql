@extends('layouts.app')

@section('title', 'Edit Package')

@section('content')
    <form action="{{ route('packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="package_name">Package Name</label>
            <input type="text" name="package_name" id="package_name" class="form-control"
                value="{{ old('package_name', $package->package_name) }}" required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control"
                value="{{ old('price', $package->price) }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Package</button>
    </form>
@endsection
