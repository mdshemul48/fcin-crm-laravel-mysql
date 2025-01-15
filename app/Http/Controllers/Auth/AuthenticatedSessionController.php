<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{

    public function showLoginForm()
    {
        return view("auth.login");
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log the user in
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->remember)) {
            // If successful, regenerate the session and redirect to the dashboard
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        // If authentication fails, redirect back with an error message
        return redirect()->back()->with('error', 'Invalid credentials');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        // Log the user out
        Auth::guard('web')->logout();

        // Invalidate and regenerate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect back to the login page after logging out
        return redirect()->route('login');
    }
}
