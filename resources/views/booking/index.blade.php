@extends('layouts.app')

@section('title', 'Booking Requests')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📣 Booking Requests</h1>
            <p class="text-sm text-gray-500 mt-1">All advertisement booking submissions.</p>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
            {{ $bookings->total() }} total
        </span>
    </div>

    @if($bookings->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <div class="text-5xl mb-4">📭</div>
            <p class="text-lg font-medium">No booking requests yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h2 class="text-base font-semibold text-gray-900 truncate">{{ $booking->event_name }}</h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $booking->status === 'pending'  ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $booking->status === 'approved' ? 'bg-green-100 text-green-800'  : '' }}
                                {{ $booking->status === 'rejected' ? 'bg-red-100 text-red-800'      : '' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">📍 {{ $booking->location }}</p>
                        @if($booking->advertise_start)
                        <p class="text-sm text-gray-500 mt-0.5">📅 Ad period:
                            <strong>{{ $booking->advertise_start->format('M j, Y') }}</strong>
                            @if($booking->advertise_end)
                                → <strong>{{ $booking->advertise_end->format('M j, Y') }}</strong>
                            @endif
                        </p>
                        @endif
                        <p class="text-sm text-gray-500 mt-0.5">⏱️
                            @if($booking->days > 0 && $booking->hours > 0)
                                {{ $booking->days }} day{{ $booking->days !== 1 ? 's' : '' }} + {{ $booking->hours }} hr{{ $booking->hours !== 1 ? 's' : '' }}
                            @elseif($booking->days > 0)
                                {{ $booking->days }} day{{ $booking->days !== 1 ? 's' : '' }}
                            @else
                                {{ $booking->hours }} hr{{ $booking->hours !== 1 ? 's' : '' }}
                            @endif
                            = {{ $booking->total_hours }} hours
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-2xl font-bold text-blue-600">₱{{ number_format($booking->total_cost, 2) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Submitted {{ $booking->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-50 flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex-1 text-sm text-gray-600 space-y-0.5">
                        <p><span class="font-medium">Contact:</span> {{ $booking->contact_name }} — <a href="mailto:{{ $booking->contact_email }}" class="text-blue-600 hover:underline">{{ $booking->contact_email }}</a>
                            @if($booking->contact_phone) · {{ $booking->contact_phone }} @endif
                        </p>
                        @if($booking->notes)
                        <p class="text-gray-400 italic">"{{ $booking->notes }}"</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
