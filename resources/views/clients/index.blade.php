@extends('layouts.app')

@section('title', 'Clients List')

@section('header_content')
    <div class="d-flex flex-column flex-md-row gap-2 align-items-center">
        <form action="{{ route('clients.index') }}" method="GET" class="d-flex">
            <div class="input-group">
                <input type="text" name="search" class="form-control border-0 shadow-sm"
                    placeholder="Search by Username, Number, or C.ID" value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <a href="{{ route('clients.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Client
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
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
        :root {
            --primary-color: #00B4B4;
            --primary-hover: #009999;
            --primary-light: rgba(0, 180, 180, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: var(--primary-light);
            color: #2c3e50;
            font-weight: 600;
            border-bottom: none;
        }

        .table tbody td {
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: var(--primary-light);
        }

        .btn {
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .input-group {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }

        .pagination {
            margin: 1rem 0;
        }

        .page-link {
            border: none;
            color: var(--primary-color);
            background: transparent;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 0.5rem;
            }
        }
    </style>
@endsection
