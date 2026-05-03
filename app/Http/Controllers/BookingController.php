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
            'advertise_end'   => 'required|date|after_or_equal:advertise_start',
            'time_start'      => 'required|date_format:H:i',
            'time_end'        => 'required|date_format:H:i|after:time_start',
            'contact_name'    => 'required|string|max:255',
            'contact_email'   => 'required|email|max:255',
            'contact_phone'   => 'nullable|string|max:30',
            'notes'           => 'nullable|string|max:1000',
        ]);

        // Compute days from date range (inclusive: May1→May3 = 3 days)
        $days = \Carbon\Carbon::parse($data['advertise_start'])
                    ->diffInDays(\Carbon\Carbon::parse($data['advertise_end'])) + 1;

        // Compute hours per day from time range
        $tStart = \Carbon\Carbon::createFromFormat('H:i', $data['time_start']);
        $tEnd   = \Carbon\Carbon::createFromFormat('H:i', $data['time_end']);
        $hoursPerDay = (int) round($tStart->floatDiffInHours($tEnd));

        if ($hoursPerDay < 1) {
            return back()->withErrors(['time_end' => 'End time must be at least 1 hour after start time.'])->withInput();
        }

        $totalHours = $days * $hoursPerDay;
        $totalCost  = $totalHours * \App\Models\Booking::RATE_PER_HOUR;

        $booking = Booking::create([
            'user_id'         => auth()->id(),
            'event_name'      => $data['event_name'],
            'location'        => $data['location'],
            'advertise_start' => $data['advertise_start'],
            'advertise_end'   => $data['advertise_end'],
            'time_start'      => $data['time_start'],
            'time_end'        => $data['time_end'],
            'days'            => $days,
            'hours'           => $hoursPerDay,
            'total_hours'     => $totalHours,
            'total_cost'      => $totalCost,
            'contact_name'    => $data['contact_name'],
            'contact_email'   => $data['contact_email'],
            'contact_phone'   => $data['contact_phone'] ?? null,
            'notes'           => $data['notes'] ?? null,
            'status'          => 'pending',
        ]);

        // Notify all admin users
        $startFormatted = \Carbon\Carbon::parse($booking->advertise_start)->format('F j, Y');
        $endFormatted   = \Carbon\Carbon::parse($booking->advertise_end)->format('F j, Y');
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            AppNotification::create([
                'user_id' => $admin->id,
                'title'   => '📣 New Booking Request',
                'message' => "{$booking->contact_name} submitted a booking for \"{$booking->event_name}\" from {$startFormatted} to {$endFormatted} ({$booking->days} days). Estimated cost: ₱" . number_format($booking->total_cost, 2),
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
