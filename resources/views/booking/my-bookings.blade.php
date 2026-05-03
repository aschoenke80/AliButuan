@extends('layouts.app')

@section('title', 'My Booking Requests')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📣 My Bookings</h1>
            <p class="text-sm text-gray-500 mt-1">Track your advertisement booking requests.</p>
        </div>
        <a href="{{ route('booking.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
            + New Booking
        </a>
    </div>

    @if($bookings->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <div class="text-5xl mb-4">📭</div>
            <p class="text-lg font-medium text-gray-700">No bookings yet.</p>
            <p class="text-sm text-gray-400 mt-1">Submit your first advertisement booking request.</p>
            <a href="{{ route('booking.create') }}" class="mt-5 inline-block px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                Book an Ad
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h2 class="text-base font-semibold text-gray-900 truncate">{{ $booking->event_name }}</h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $booking->status === 'pending'  ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800'  : '' }}
                                {{ $booking->status === 'rejected' ? 'bg-red-100 text-red-800'      : '' }}">
                                @if($booking->status === 'pending')  ⏳ Pending Review
                                @elseif($booking->status === 'approved') ✅ Approved
                                @else ❌ Not Approved
                                @endif
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">📍 {{ $booking->location }}</p>
                        @if($booking->advertise_start && $booking->advertise_end)
                        <p class="text-sm text-gray-500 mt-0.5">
                            📅 {{ $booking->advertise_start->format('M j, Y') }}
                            → {{ $booking->advertise_end->format('M j, Y') }}
                            ({{ $booking->days }} day{{ $booking->days !== 1 ? 's' : '' }})
                        </p>
                        @endif
                        @if($booking->time_start && $booking->time_end)
                        <p class="text-sm text-gray-500 mt-0.5">
                            🕐 {{ \Carbon\Carbon::createFromFormat('H:i', $booking->time_start)->format('g:i A') }}
                            – {{ \Carbon\Carbon::createFromFormat('H:i', $booking->time_end)->format('g:i A') }}
                        </p>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-xl font-bold text-blue-600">₱{{ number_format($booking->total_cost, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $booking->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                @if($booking->status === 'pending')
                <div class="mt-3 pt-3 border-t border-gray-50">
                    <p class="text-xs text-yellow-700 bg-yellow-50 rounded-lg px-3 py-2">
                        ⏳ Your booking is being reviewed by our organizer. We'll notify you once a decision is made.
                    </p>
                </div>
                @elseif($booking->status === 'approved')
                <div class="mt-3 pt-3 border-t border-gray-50">
                    <p class="text-xs text-green-700 bg-green-50 rounded-lg px-3 py-2">
                        ✅ Your booking has been approved! Please wait for our organizer to contact you with payment details.
                    </p>
                </div>
                @elseif($booking->status === 'rejected')
                <div class="mt-3 pt-3 border-t border-gray-50">
                    <p class="text-xs text-red-700 bg-red-50 rounded-lg px-3 py-2">
                        ❌ This booking was not approved. You may submit a new booking request with different details.
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
