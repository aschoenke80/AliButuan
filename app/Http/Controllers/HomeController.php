<?php

namespace App\Http\Controllers;

use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured approved events
        $featuredEvents = Event::approved()
            ->featured()
            ->orderBy('start_datetime')
            ->take(6)
            ->get();

        // Get upcoming approved events (not featured, soonest first)
        $upcomingEvents = Event::approved()
            ->where('start_datetime', '>=', now())
            ->orderBy('start_datetime')
            ->take(8)
            ->get();

        return view('home', compact('featuredEvents', 'upcomingEvents'));
    }
}
