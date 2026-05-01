<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Show the registration form
    public function create()
    {
        return view('auth.register');
    }

    // Handle registration form submission
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role'         => 'required|in:user,organizer',
        ]);

        // Create the new user
        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'role'         => $request->role, // user or organizer
        ]);

        // Log them in automatically
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Welcome to AliButuan, ' . $user->name . '!');
    }
}
