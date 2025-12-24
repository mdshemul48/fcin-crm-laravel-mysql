@extends('layouts.app')

@section('title', 'Clients List')

@section('header_content')
    <div class="d-flex flex-column flex-md-row gap-2 align-items-center">
        <div class="d-flex gap-2">
            <div class="input-group">
                <select id="paymentStatus" class="form-select border-0 shadow-sm w-auto">
                    <option value="">All Clients</option>
                    <option value="paid" {{ request()->query('payment_status') === 'paid' ? 'selected' : '' }}>Paid
                    </option>
                    <option value="due" {{ request()->query('payment_status') === 'due' ? 'selected' : '' }}>Unpaid
                    </option>
                    <option value="active" {{ request()->query('billing_status') === 'active' ? 'selected' : '' }}>Active
                    </option>
                    <option value="inactive" {{ request()->query('billing_status') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
                <input type="text" id="searchInput" class="form-control border-0 shadow-sm w-auto"
                    placeholder="Search by Username, Number, or C.ID" value="{{ request()->query('search') }}">
            </div>
        </div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Client
        </a>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-0">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0" id="table-container">
                @include('clients.list.table-content')
            </div>
        </div>
    </div>
    <script>
        console.log("gg")
    </script>
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

@section('custom-scripts')
    <script>
        $(document).ready(function() {
            let searchTimeout;

            function performSearch() {
                const search = $('#searchInput').val();
                const status = $('#paymentStatus').val();

                const currentUrl = window.location.href.split('?')[0];
                const params = new URLSearchParams();

                if (search) params.set('search', search);

                // Handle payment status (paid, due) or billing status (active, inactive)
                // Clear the other filter when switching between them
                if (status === 'active' || status === 'inactive') {
                    params.set('billing_status', status);
                    // Clear payment_status if it was set
                    params.delete('payment_status');
                } else if (status) {
                    params.set('payment_status', status);
                    // Clear billing_status if it was set
                    params.delete('billing_status');
                } else {
                    // Clear both when "All Clients" is selected
                    // Note: "All Clients" will still only show active clients by default
                    params.delete('payment_status');
                    params.delete('billing_status');
                }

                const url = `${currentUrl}${params.toString() ? '?' + params.toString() : ''}`;

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    beforeSend: function() {
                        $('#table-container').addClass('opacity-50');
                    },
                    success: function(response) {
                        $('#table-container').html(response.html);
                        history.pushState({}, '', response.url);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        toastr.error('An error occurred while searching');
                    },
                    complete: function() {
                        $('#table-container').removeClass('opacity-50');
                    }
                });
            }

            let debounceTimer;
            $('#searchInput').on('input', function() {
                clearTimeout(debounceTimer);
                if ($(this).val().length >= 3 || $(this).val().length === 0) {
                    debounceTimer = setTimeout(performSearch, 500);
                }
            });

            $('#paymentStatus').on('change', performSearch);
        });
    </script>
@endsection
