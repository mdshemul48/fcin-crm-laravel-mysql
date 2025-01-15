@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}"
                required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control"
                value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Password (Leave blank if you don't want to change)</label>
            <input type="password" name="password" id="password" class="form-control">
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Role -->
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" id="role" class="form-control" required>
                <option value="admin" @if ($user->role == 'admin') selected @endif>Admin</option>
                <option value="support" @if ($user->role == 'support') selected @endif>Support</option>
                <option value="user" @if ($user->role == 'user') selected @endif>User</option>
            </select>
            @error('role')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Active / Inactive -->
        <div class="mb-3">
            <label for="isActive" class="form-label">Status</label>
            <select name="isActive" id="isActive" class="form-control" required>
                <option value="1" @if ($user->isActive) selected @endif>Active</option>
                <option value="0" @if (!$user->isActive) selected @endif>Inactive</option>
            </select>
            @error('isActive')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
@endsection
