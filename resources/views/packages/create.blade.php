@extends('layouts.app')

@section('title', 'Create Package')

@section('content')
    <form action="{{ route('packages.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Package Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Create Package</button>
    </form>
@endsection
