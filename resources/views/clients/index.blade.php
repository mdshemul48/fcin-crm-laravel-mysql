@extends('layouts.app')

@section('title', 'Clients List')

@section('header_content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
        <form action="{{ route('clients.index') }}" method="GET" class="d-flex ms-3 mb-2 mb-md-0">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by Username, Number, or C.ID"
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
        <a href="{{ route('clients.create') }}" class="btn btn-primary ms-1">
            <i class="bi bi-plus-lg me-1"></i> Add Client
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-1">
                @include('clients.list.desktop')
                @include('clients.list.mobile')
                <div class="d-flex justify-content-center mt-4">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }

        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: black;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
    </style>
@endsection
