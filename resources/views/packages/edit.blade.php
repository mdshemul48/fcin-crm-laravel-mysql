@extends('layouts.app')

@section('title', 'Edit Package')

@section('content')
    <form action="{{ route('packages.update', $package) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Package Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $package->name) }}"
                required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control"
                value="{{ old('price', $package->price) }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Package</button>
    </form>
@endsection
