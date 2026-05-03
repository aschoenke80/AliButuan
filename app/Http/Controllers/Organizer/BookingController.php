<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    // List all booking requests submitted by users
    public function index()
    {
        $pending  = Booking::with('user')->where('status', 'pending')->latest()->get();
        $reviewed = Booking::with('user')->whereIn('status', ['approved', 'rejected'])->latest()->paginate(15);

        return view('organizer.bookings.index', compact('pending', 'reviewed'));
    }

    // Approve a booking and notify the user
    public function approve(Booking $booking)
    {
        $booking->update(['status' => 'approved']);

        AppNotification::create([
            'user_id' => $booking->user_id,
            'title'   => '✅ Booking Approved!',
            'message' => "Your advertisement booking for \"{$booking->event_name}\" has been approved! " .
                         "Period: " . $booking->advertise_start->format('M j') . " – " . $booking->advertise_end->format('M j, Y') . ". " .
                         "Estimated cost: ₱" . number_format($booking->total_cost, 2) . ". We will contact you soon.",
            'type'    => 'booking',
            'is_read' => false,
        ]);

        return back()->with('success', "Booking for \"{$booking->event_name}\" has been approved.");
    }

    // Reject a booking and notify the user
    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'reject_reason' => 'nullable|string|max:500',
        ]);

        $booking->update(['status' => 'rejected']);

        $reason = $request->filled('reject_reason')
            ? " Reason: " . $request->reject_reason
            : '';

        AppNotification::create([
            'user_id' => $booking->user_id,
            'title'   => '❌ Booking Not Approved',
            'message' => "Your advertisement booking for \"{$booking->event_name}\" was not approved at this time.{$reason}",
            'type'    => 'booking',
            'is_read' => false,
        ]);

        return back()->with('success', "Booking for \"{$booking->event_name}\" has been rejected.");
    }
}
