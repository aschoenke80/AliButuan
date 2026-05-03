<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create()
    {
        return view('booking.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_name'      => 'required|string|max:255',
            'location'        => 'required|string|max:255',
            'advertise_start' => 'required|date|after_or_equal:today',
            'days'            => 'required|integer|min:0',
            'hours'           => 'required|integer|min:0|max:23',
            'contact_name'    => 'required|string|max:255',
            'contact_email'   => 'required|email|max:255',
            'contact_phone'   => 'nullable|string|max:30',
            'notes'           => 'nullable|string|max:1000',
        ]);

        // Must have at least 1 hour total
        if (($data['days'] * 24 + $data['hours']) < 1) {
            return back()->withErrors(['hours' => 'Please enter at least 1 hour or 1 day.'])->withInput();
        }

        $computed = Booking::computeCost((int)$data['days'], (int)$data['hours']);

        $booking = Booking::create([
            'user_id'         => auth()->id(),
            'event_name'      => $data['event_name'],
            'location'        => $data['location'],
            'advertise_start' => $data['advertise_start'],
            'days'            => $data['days'],
            'hours'           => $data['hours'],
            'total_hours'     => $computed['totalHours'],
            'total_cost'      => $computed['totalCost'],
            'contact_name'    => $data['contact_name'],
            'contact_email'   => $data['contact_email'],
            'contact_phone'   => $data['contact_phone'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => 'pending',
        ]);

        // Notify all admin users
        $startFormatted = \Carbon\Carbon::parse($booking->advertise_start)->format('F j, Y');
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            AppNotification::create([
                'user_id' => $admin->id,
                'title'   => '📣 New Booking Request',
                'message' => "{$booking->contact_name} submitted a booking for \"{$booking->event_name}\" starting {$startFormatted}. Estimated cost: ₱" . number_format($booking->total_cost, 2),
                'type'    => 'booking',
                'is_read' => false,
            ]);
        }

        return redirect()->route('booking.create')
            ->with('success', 'Your booking request has been submitted! We will contact you shortly.');
    }

    public function index()
    {
        // Admin only — show all bookings
        $bookings = Booking::with('user')->latest()->paginate(20);
        return view('booking.index', compact('bookings'));
    }
}
