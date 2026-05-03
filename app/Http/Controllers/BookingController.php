<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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
            'event_name'    => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'days'          => 'required|integer|min:0',
            'hours'         => 'required|integer|min:0|max:23',
            'contact_name'  => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'notes'         => 'nullable|string|max:1000',
        ]);

        // Must have at least 1 hour total
        if (($data['days'] * 24 + $data['hours']) < 1) {
            return back()->withErrors(['hours' => 'Please enter at least 1 hour or 1 day.'])->withInput();
        }

        $computed = Booking::computeCost((int)$data['days'], (int)$data['hours']);

        Booking::create([
            'user_id'       => auth()->id(),
            'event_name'    => $data['event_name'],
            'location'      => $data['location'],
            'days'          => $data['days'],
            'hours'         => $data['hours'],
            'total_hours'   => $computed['totalHours'],
            'total_cost'    => $computed['totalCost'],
            'contact_name'  => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'] ?? null,
            'notes'         => $data['notes'] ?? null,
            'status'        => 'pending',
        ]);

        return redirect()->route('booking.create')
            ->with('success', 'Your booking request has been submitted! We will contact you shortly.');
    }
}
