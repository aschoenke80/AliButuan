<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // List all events with filter by status
    public function index(Request $request)
    {
        $query = Event::with('organizer')->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->paginate(20)->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    // Approve an event and notify the organizer
    public function approve(Event $event)
    {
        $event->update(['status' => 'approved']);

        AppNotification::create([
            'user_id' => $event->organizer_id,
            'title'   => 'Event Approved! 🎉',
            'message' => "Your event \"{$event->title}\" has been approved and is now live.",
            'type'    => 'event_approved',
        ]);

        return back()->with('success', "Event \"{$event->title}\" approved.");
    }

    // Reject an event and notify the organizer
    public function reject(Request $request, Event $event)
    {
        $event->update(['status' => 'rejected']);

        AppNotification::create([
            'user_id' => $event->organizer_id,
            'title'   => 'Event Rejected',
            'message' => "Your event \"{$event->title}\" was not approved. Please review and resubmit.",
            'type'    => 'event_rejected',
        ]);

        return back()->with('success', "Event \"{$event->title}\" rejected.");
    }

    // Toggle the featured flag
    public function feature(Event $event)
    {
        $event->update(['is_featured' => !$event->is_featured]);

        $status = $event->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Event \"{$event->title}\" is now {$status}.");
    }

    // Delete an event
    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return back()->with('success', 'Event deleted.');
    }
}
