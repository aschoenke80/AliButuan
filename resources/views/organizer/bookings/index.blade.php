@extends('layouts.app')

@section('title', 'Booking Requests')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📣 Booking Requests</h1>
            <p class="text-sm text-gray-500 mt-1">Review advertisement booking submissions from users.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
            <span class="text-xl">✅</span>
            <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ── PENDING ──────────────────────────────────────────────────────── --}}
    <div class="mb-10">
        <div class="flex items-center gap-2 mb-4">
            <h2 class="text-base font-semibold text-gray-800">Pending</h2>
            @if($pending->count() > 0)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700">
                    {{ $pending->count() }}
                </span>
            @endif
        </div>

        @forelse($pending as $booking)
        <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-5 mb-4">
            @include('organizer.bookings._card', ['booking' => $booking, 'showActions' => true])
        </div>
        @empty
        <div class="text-center py-10 bg-white rounded-2xl border border-gray-100">
            <p class="text-gray-400 text-sm">No pending booking requests. 🎉</p>
        </div>
        @endforelse
    </div>

    {{-- ── REVIEWED ─────────────────────────────────────────────────────── --}}
    <div>
        <h2 class="text-base font-semibold text-gray-800 mb-4">Reviewed</h2>

        @forelse($reviewed as $booking)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4 opacity-80">
            @include('organizer.bookings._card', ['booking' => $booking, 'showActions' => false])
        </div>
        @empty
        <p class="text-center text-gray-400 text-sm py-6">No reviewed bookings yet.</p>
        @endforelse

        <div class="mt-6">{{ $reviewed->links() }}</div>
    </div>
</div>
@endsection
