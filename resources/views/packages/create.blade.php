@extends('layouts.app')

@section('title', 'Create Package')

@section('content')
    <form action="{{ route('packages.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="package_name">Package Name</label>
            <input type="text" name="package_name" id="package_name" class="form-control" value="{{ old('package_name') }}"
                required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Package</button>
    </form>
@endsection
