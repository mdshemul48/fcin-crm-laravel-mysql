@extends('layouts.app')

@section('title', 'Packages')

@section('content')
    <a href="{{ route('packages.create') }}" class="btn btn-primary">Add Package</a>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Package Name</th>
                <th>Price</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($packages as $package)
                <tr>
                    <td>{{ $package->package_name }}</td>
                    <td>{{ $package->price }}</td>
                    <td>{{ $package->created_by ? $package->createdBy->name : 'N/A' }}</td>
                    <td>
                        <a href="{{ route('packages.edit', $package) }}" class="btn btn-warning">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $packages->links() }}
@endsection
