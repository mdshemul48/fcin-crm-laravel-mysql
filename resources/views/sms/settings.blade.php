@extends('layouts.app')

@section('title', 'SMS Settings')

@section('content')
    <div class="container-fluid">
        <div class="mb-3 d-flex justify-content-end">
            <a href="{{ route('sms.logs') }}" class="btn btn-info">
                <i class="fas fa-history"></i> View SMS Logs
            </a>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">SMS Gateway Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Current Balance:</strong> {{ $balance ?? '0.00' }} Taka
                        </div>

                        <form action="{{ route('sms.settings.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="gateway_name" class="form-label">Gateway Name</label>
                                <input type="text" class="form-control" id="gateway_name" name="gateway_name"
                                    value="{{ $settings->gateway_name ?? '' }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="api_key" class="form-label">API Key</label>
                                <input type="text" class="form-control" id="api_key" name="api_key"
                                    value="{{ $settings->api_key ?? '' }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="secret_key" class="form-label">Secret Key</label>
                                <input type="text" class="form-control" id="secret_key" name="secret_key"
                                    value="{{ $settings->secret_key ?? '' }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="caller_id" class="form-label">Caller ID</label>
                                <input type="text" class="form-control" id="caller_id" name="caller_id"
                                    value="{{ $settings->caller_id ?? '' }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="client_id" class="form-label">Client ID</label>
                                <input type="text" class="form-control" id="client_id" name="client_id"
                                    value="{{ $settings->client_id ?? '' }}">
                            </div>

                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">SMS Templates</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sms.templates.store') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Template Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Template Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="payment">Payment</option>
                                    <option value="bill">Bill</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Template Content</label>
                                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                                <small class="text-muted">
                                    <strong>Available variables:</strong><br>
                                    <strong>Client Info:</strong> {client_id}, {username}, {status}, {phone_number},
                                    {address}, {current_balance}, {due_amount}, {bill_amount}<br>
                                    <strong>Payment Info:</strong> {amount}, {discount}, {payment_date}, {payment_type},
                                    {month}, {year}, {remarks}<br>
                                    <strong>Package Info:</strong> {package_name}, {package_price}
                                </small>
                            </div>

                            <button type="submit" class="btn btn-success">Add Template</button>
                        </form>

                        <hr>

                        <h6>Existing Templates</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Content</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($templates as $template)
                                        <tr>
                                            <td>{{ $template->name }}</td>
                                            <td><span class="badge bg-info">{{ $template->type }}</span></td>
                                            <td>{{ Str::limit($template->content, 50) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit-template" data-bs-toggle="modal"
                                                    data-bs-target="#editTemplateModal" data-id="{{ $template->id }}"
                                                    data-name="{{ $template->name }}" data-type="{{ $template->type }}"
                                                    data-content="{{ $template->content }}">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger delete-template"
                                                    data-id="{{ $template->id }}">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Template Modal -->
    <div class="modal fade" id="editTemplateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editTemplateForm">
                        <input type="hidden" id="edit_template_id">
                        <div class="mb-3">
                            <label class="form-label">Template Name</label>
                            <input type="text" class="form-control" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Template Type</label>
                            <select class="form-select" id="edit_type" required>
                                <option value="payment">Payment</option>
                                <option value="bill">Bill</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Template Content</label>
                            <textarea class="form-control" id="edit_content" rows="4" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateTemplate">Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .card {
            border: none;
            margin-bottom: 1.5rem;
        }

        .card-header {
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, .25);
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
        }

        .table {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
        }
    </style>
@endsection

@section('custom-scripts')
    <script>
        $(document).ready(function() {
            // Handle Edit Template Modal
            $('.edit-template').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const type = $(this).data('type');
                const content = $(this).data('content');

                $('#edit_template_id').val(id);
                $('#edit_name').val(name);
                $('#edit_type').val(type);
                $('#edit_content').val(content);
            });

            // Handle Template Update
            $('#updateTemplate').on('click', function() {
                const id = $('#edit_template_id').val();
                const data = {
                    name: $('#edit_name').val(),
                    type: $('#edit_type').val(),
                    content: $('#edit_content').val(),
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                $.ajax({
                    url: `/sms/templates/${id}`,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        $('#editTemplateModal').modal('hide');
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1000);
                    },
                    error: function(xhr) {
                        toastr.error('Error updating template');
                    }
                });
            });

            // Handle Template Delete
            $('.delete-template').on('click', function() {
                if (!confirm('Are you sure you want to delete this template?')) return;

                const id = $(this).data('id');

                $.ajax({
                    url: `/sms/templates/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        setTimeout(() => window.location.reload(), 1000);
                    },
                    error: function(xhr) {
                        toastr.error('Error deleting template');
                    }
                });
            });
        });
    </script>
@endsection
