<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Event;

class UserController extends Controller
{
    // Show the profile edit form
    public function edit()
    {
        $user = auth()->user();

        $stats = [];

        if ($user->role === 'admin') {
            $stats = [
                'total_users'    => User::count(),
                'total_events'   => Event::count(),
                'pending_events' => Event::where('status', 'pending')->count(),
                'approved_events'=> Event::where('status', 'approved')->count(),
            ];
        } elseif ($user->role === 'organizer') {
            $stats = [
                'my_events'   => $user->events()->count(),
                'approved'    => $user->events()->where('status', 'approved')->count(),
                'pending'     => $user->events()->where('status', 'pending')->count(),
                'rejected'    => $user->events()->where('status', 'rejected')->count(),
            ];
        } else {
            $stats = [
                'favorites'     => $user->favorites()->count(),
                'notifications' => $user->notifications()->where('is_read', false)->count(),
            ];
        }

        return view('user.profile', compact('user', 'stats'));
    }

    // Update profile info (name, email, phone)
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Change password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Verify the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully!');
    }
}
