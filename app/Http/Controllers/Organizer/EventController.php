<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // Organizer dashboard with stats
    public function dashboard()
    {
        $userId = auth()->id();

        $stats = [
            'total'    => Event::where('organizer_id', $userId)->count(),
            'pending'  => Event::where('organizer_id', $userId)->where('status', 'pending')->count(),
            'approved' => Event::where('organizer_id', $userId)->where('status', 'approved')->count(),
            'rejected' => Event::where('organizer_id', $userId)->where('status', 'rejected')->count(),
        ];

        $recentEvents = Event::where('organizer_id', $userId)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('organizer.dashboard', compact('stats', 'recentEvents'));
    }

    // List all events by this organizer
    public function index()
    {
        $events = Event::where('organizer_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('organizer.events.index', compact('events'));
    }

    // Show the create event form
    public function create()
    {
        $categories = array_keys(Event::CATEGORIES);
        return view('organizer.events.create', compact('categories'));
    }

    // Save a new event
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|in:' . implode(',', array_keys(Event::CATEGORIES)),
            'audience'       => 'nullable|string|max:100',
            'location_name'  => 'required|string|max:255',
            'latitude'       => 'required|numeric|between:-90,90',
            'longitude'      => 'required|numeric|between:-180,180',
            'start_datetime' => 'required|date|after:now',
            'end_datetime'   => 'required|date|after:start_datetime',
            'image'          => 'nullable|image|max:2048',
        ]);

        $data['organizer_id'] = auth()->id();
        $data['status']       = 'pending';

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($data);

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event submitted! It will be visible after admin approval.');
    }

    // Show the edit form for an event
    public function edit(Event $event)
    {
        // Only allow editing own pending or rejected events
        $this->authorizeEvent($event);

        if ($event->status === 'approved') {
            return back()->with('error', 'Approved events cannot be edited.');
        }

        $categories = array_keys(Event::CATEGORIES);
        return view('organizer.events.edit', compact('event', 'categories'));
    }

    // Update an existing event
    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        if ($event->status === 'approved') {
            return back()->with('error', 'Approved events cannot be edited.');
        }

        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|in:' . implode(',', array_keys(Event::CATEGORIES)),
            'audience'       => 'nullable|string|max:100',
            'location_name'  => 'required|string|max:255',
            'latitude'       => 'required|numeric|between:-90,90',
            'longitude'      => 'required|numeric|between:-180,180',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:start_datetime',
            'image'          => 'nullable|image|max:2048',
        ]);

        // Handle new image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        // Reset status to pending on update so admin re-reviews
        $data['status'] = 'pending';

        $event->update($data);

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event updated and re-submitted for approval.');
    }

    // Delete a pending or rejected event
    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);

        if ($event->status === 'approved') {
            return back()->with('error', 'Approved events cannot be deleted.');
        }

        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event deleted.');
    }

    // Make sure the organizer owns this event
    private function authorizeEvent(Event $event)
    {
        if ($event->organizer_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not own this event.');
        }
    }
}
