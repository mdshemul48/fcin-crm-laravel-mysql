<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Display all users
    public function index()
    {
        $users = User::all();
        return view('user-management.index', compact('users'));
    }

    // Show the form for creating a new user
    public function create()
    {
        return view('user-management.create');
    }

    // Store a newly created user
    public function store(Request $request)
    {
        // dd("gg");
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,support,user',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    // Show the form for editing an existing user
    public function edit(User $user)
    {
        return view('user-management.edit', compact('user'));
    }

    // Update an existing user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,support,user',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($validated['password']) {
            $user->password = Hash::make($validated['password']);
        }
        $user->role = $validated['role'];
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    // Delete a user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
