<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // List all users and organizers
    public function index(Request $request)
    {
        $query = User::orderByDesc('created_at');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // Promote a user to organizer (or demote)
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,organizer',
        ]);

        // Prevent changing admin's own role
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot change an admin\'s role.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "User \"{$user->name}\" is now a {$request->role}.");
    }
}
