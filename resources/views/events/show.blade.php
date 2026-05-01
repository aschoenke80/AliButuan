@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Back link --}}
    <a href="{{ route('events.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-blue-600 mb-6 transition-colors">
        ← Back to Events
    </a>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Hero image --}}
        @if($event->image)
            <div class="h-72 md:h-96 overflow-hidden">
                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                     class="w-full h-full object-cover" />
            </div>
        @else
            <div class="h-48 bg-gradient-to-br from-blue-50 to-sky-100 flex items-center justify-center text-8xl">
                {{ $event->category_emoji }}
            </div>
        @endif

        <div class="p-6 md:p-10">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">

                {{-- Main info --}}
                <div class="flex-1">
                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                            {{ $event->category_emoji }} {{ $event->category }}
                        </span>
                        @if($event->is_featured)
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                ⭐ Featured
                            </span>
                        @endif
                        @if($event->audience)
                            <span class="bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1 rounded-full">
                                👥 {{ $event->audience }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">{{ $event->title }}</h1>

                    {{-- Details --}}
                    <div class="space-y-3 mb-6">
                        <div class="flex items-start gap-3">
                            <span class="text-xl">📅</span>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Date & Time</p>
                                <p class="text-sm text-gray-600">
                                    {{ $event->start_datetime->format('l, F d, Y') }} —
                                    {{ $event->start_datetime->format('h:i A') }} to {{ $event->end_datetime->format('h:i A') }}
                                </p>
                                @if(!$event->start_datetime->isSameDay($event->end_datetime))
                                    <p class="text-xs text-gray-400">Ends: {{ $event->end_datetime->format('l, F d, Y') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-xl">📍</span>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Location</p>
                                <p class="text-sm text-gray-600">{{ $event->location_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-xl">👤</span>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Organizer</p>
                                <p class="text-sm text-gray-600">{{ $event->organizer->name }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">About this Event</h2>
                        <div class="text-sm text-gray-600 leading-relaxed prose max-w-none">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-col gap-3 min-w-40">
                    @auth
                        {{-- Save / Unsave button --}}
                        <form method="POST" action="{{ route('favorites.toggle', $event->id) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm transition-colors
                                           {{ $isFavorited ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' : 'bg-green-50 text-green-600 border border-green-200 hover:bg-green-100' }}">
                                {{ $isFavorited ? '❤️ Saved' : '🤍 Save Event' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-50 text-green-600 border border-green-200 rounded-xl font-medium text-sm hover:bg-green-100 transition-colors">
                            🤍 Save Event
                        </a>
                    @endauth

                    {{-- Share button --}}
                    <button onclick="shareEvent()"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-600 border border-blue-200 rounded-xl font-medium text-sm hover:bg-blue-100 transition-colors">
                        🔗 Share Event
                    </button>

                    {{-- View on map --}}
                    <a href="{{ route('map.index') }}?lat={{ $event->latitude }}&lng={{ $event->longitude }}"
                       class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-50 text-sky-600 border border-sky-200 rounded-xl font-medium text-sm hover:bg-sky-100 transition-colors">
                        🗺️ View on Map
                    </a>
                </div>
            </div>

            {{-- Mini map showing the event location --}}
            <div class="mt-8 border-t border-gray-100 pt-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">📍 Event Location</h2>
                <div id="event-map" class="h-64 rounded-xl border border-gray-200 overflow-hidden z-0"></div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Show the event location on a small map restricted to Butuan City
    const BUTUAN_BOUNDS = L.latLngBounds(
        L.latLng(8.88, 125.47),
        L.latLng(9.02, 125.62)
    );
    const map = L.map('event-map', {
        zoomControl: true,
        maxBounds: BUTUAN_BOUNDS,
        maxBoundsViscosity: 1.0,
        minZoom: 12,
    }).setView(
        [{{ $event->latitude }}, {{ $event->longitude }}], 15
    );

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors © <a href="https://carto.com">CARTO</a>'
    }).addTo(map);

    const emoji = '{{ $event->category_emoji }}';
    const icon = L.divIcon({
        html: `<div style="font-size:28px;line-height:1;">${emoji}</div>`,
        iconSize: [32, 32],
        iconAnchor: [16, 16],
        className: ''
    });

    L.marker([{{ $event->latitude }}, {{ $event->longitude }}], { icon })
        .addTo(map)
        .bindPopup('<strong>{{ addslashes($event->title) }}</strong><br>{{ addslashes($event->location_name) }}')
        .openPopup();

    // Share event using Web Share API, fallback to clipboard
    function shareEvent() {
        const data = {
            title: '{{ addslashes($event->title) }}',
            text: 'Check out this event in Butuan City!',
            url: window.location.href
        };
        if (navigator.share) {
            navigator.share(data);
        } else {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Event link copied to clipboard!');
            });
        }
    }
</script>
@endpush
