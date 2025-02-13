@extends('layouts.app')

@section('title', 'SMS Logs')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">SMS Logs</h5>
                <a href="{{ route('sms.settings') }}" class="btn btn-light btn-sm">Back to Settings</a>
            </div>
            <div class="card-body">
                <form class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search by client name or phone" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered
                            </option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Message</th>
                                <th>Response</th>
                                <th>Status</th>
                                <th>Message ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <strong>{{ $log->client->name }}</strong><br>
                                        <small class="text-muted">{{ $log->client->phone_number }}</small>
                                    </td>
                                    <td>{{ Str::limit($log->content, 160) }}</td>
                                    <td>{{ Str::limit($log->response, 160) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $log->status == 'delivered' ? 'success' : ($log->status == 'sent' ? 'info' : ($log->status == 'pending' ? 'warning' : 'danger')) }}">
                                            {{ ucfirst($log->status) }}
                                        </span>
                                    </td>

                                    <td><small>{{ $log->message_id ?? 'N/A' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
