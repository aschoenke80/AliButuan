<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFavorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // Show the user's saved events
    public function index()
    {
        $favorites = auth()->user()->favorites()->with('event.organizer')->get();
        return view('user.favorites', compact('favorites'));
    }

    // Toggle save/unsave for an event
    public function toggle(Request $request, Event $event)
    {
        $userId  = auth()->id();
        $eventId = $event->id;

        $existing = EventFavorite::where('user_id', $userId)->where('event_id', $eventId)->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
        } else {
            EventFavorite::create([
                'user_id'    => $userId,
                'event_id'   => $eventId,
                'created_at' => now(),
            ]);
            $saved = true;
        }

        // If it's an AJAX request, return JSON
        if ($request->expectsJson()) {
            return response()->json(['saved' => $saved]);
        }

        return back()->with('success', $saved ? 'Event saved to favorites!' : 'Event removed from favorites.');
    }
}
