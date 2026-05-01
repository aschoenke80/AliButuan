<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalUsers'      => User::where('role', 'user')->count(),
            'totalOrganizers' => User::where('role', 'organizer')->count(),
            'totalEvents'     => Event::count(),
            'pendingEvents'   => Event::where('status', 'pending')->count(),
            'approvedEvents'  => Event::where('status', 'approved')->count(),
        ];

        $recentEvents = Event::with('organizer')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEvents'));
    }
}
