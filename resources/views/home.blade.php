@extends('layouts.app')

@section('title', 'Home')

@section('content')

    {{-- ── HERO ──────────────────────────────────────────────────────────── --}}
    <section class="bg-gradient-to-br from-blue-600 via-blue-500 to-sky-400 text-white py-12 md:py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-3xl md:text-5xl font-bold mb-3 md:mb-4 leading-tight">
                Discover Events in <span class="text-yellow-300">Butuan City</span> 🌟
            </h1>
            <p class="text-blue-100 text-base md:text-lg max-w-2xl mx-auto mb-6 md:mb-8">
                Your go-to guide for local festivals, markets, concerts, sports events, and more.
            </p>

            {{-- Quick Search --}}
            <form action="{{ route('events.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 max-w-lg mx-auto">
                <input type="text" name="search" placeholder="Search events or places..."
                       class="flex-1 px-4 py-3 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-300" />
                <button type="submit"
                        class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-semibold px-6 py-3 rounded-xl transition-colors text-sm">
                    Search
                </button>
            </form>
        </div>
    </section>

    {{-- ── CATEGORY PILLS ────────────────────────────────────────────────── --}}
    <section class="bg-white border-b border-gray-100 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap gap-2 justify-center">
                @foreach (\App\Models\Event::CATEGORIES as $cat => $emoji)
                    <a href="{{ route('events.index', ['category' => $cat]) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-50 hover:bg-blue-50 hover:text-blue-600 border border-gray-200 hover:border-blue-200 rounded-full text-sm font-medium text-gray-600 transition-colors">
                        {{ $emoji }} {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-14">

        {{-- ── FEATURED EVENTS ────────────────────────────────────────────── --}}
        @if($featuredEvents->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">⭐ Featured Events</h2>
                        <p class="text-gray-500 text-sm mt-1">Handpicked events you don't want to miss</p>
                    </div>
                    <a href="{{ route('events.index') }}" class="text-sm text-blue-600 font-medium hover:underline">View all →</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($featuredEvents as $event)
                        @include('partials.event-card', ['event' => $event, 'featured' => true])
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ── UPCOMING EVENTS ────────────────────────────────────────────── --}}
        <section>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">📅 Upcoming Events</h2>
                    <p class="text-gray-500 text-sm mt-1">What's happening in Butuan City soon</p>
                </div>
                <a href="{{ route('events.index') }}" class="text-sm text-blue-600 font-medium hover:underline">View all →</a>
            </div>

            @if($upcomingEvents->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($upcomingEvents as $event)
                        @include('partials.event-card', ['event' => $event])
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                    <p class="text-4xl mb-3">📭</p>
                    <p class="text-gray-500">No upcoming events yet. Check back soon!</p>
                    @auth
                        @if(auth()->user()->isOrganizer() || auth()->user()->isAdmin())
                        <a href="{{ route('organizer.events.create') }}" class="mt-4 inline-block bg-blue-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Submit an Event
                        </a>
                        @endif
                    @endauth
                </div>
            @endif
        </section>

        {{-- ── MAP CTA ─────────────────────────────────────────────────────── --}}
        <section class="bg-gradient-to-r from-green-500 to-emerald-400 rounded-2xl p-6 md:p-8 text-white text-center md:text-left">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 md:gap-6">
                <div>
                    <h3 class="text-xl md:text-2xl font-bold mb-2">🗺️ Explore on the Map</h3>
                    <p class="text-green-100 text-sm md:text-base">See all events pinned on an interactive map of Butuan City.</p>
                </div>
                <a href="{{ route('map.index') }}"
                   class="shrink-0 bg-white text-green-600 font-semibold px-6 py-3 rounded-xl hover:bg-green-50 transition-colors text-sm">
                    Open Map →
                </a>
            </div>
        </section>

    </div>
@endsection
