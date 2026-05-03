<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFavorite;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Show list of all approved events, with search and filter
    public function index(Request $request)
    {
        $isPrivileged = auth()->check() && in_array(auth()->user()->role, ['admin', 'organizer']);
        $showArchived = $isPrivileged && $request->get('filter') === 'archived';

        if ($showArchived) {
            $query = Event::archived()->with('organizer')->orderByDesc('end_datetime');
        } else {
            $query = Event::approved()->with('organizer')->orderBy('start_datetime');
        }

        // Search by title or location name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location_name', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        $events     = $query->paginate(12)->withQueryString();
        $categories = array_keys(Event::CATEGORIES);

        return view('events.index', compact('events', 'categories', 'showArchived', 'isPrivileged'));
    }

    // Show a single event
    public function show(Event $event)
    {
        $isPrivileged = auth()->check() && in_array(auth()->user()->role, ['admin', 'organizer']);

        // Allow organizer/admin to view any event; public only sees approved non-archived
        if (!$isPrivileged) {
            if ($event->status !== 'approved' || $event->is_archived) {
                abort(404);
            }
        }

        $isFavorited = false;
        if (auth()->check()) {
            $isFavorited = $event->isFavoritedBy(auth()->id());
        }

        return view('events.show', compact('event', 'isFavorited'));
    }
}
