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
        @php
            $canManage = auth()->check() && (auth()->user()->role === 'admin' || (auth()->user()->role === 'organizer' && $event->organizer_id === auth()->id()));
        @endphp

        @if($canManage)
            {{-- Clickable image upload for organizer/admin --}}
            <form id="image-upload-form" method="POST" action="{{ route('organizer.events.update', $event->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                {{-- Hidden fields to keep other data intact --}}
                <input type="hidden" name="title" value="{{ $event->title }}">
                <input type="hidden" name="description" value="{{ $event->description }}">
                <input type="hidden" name="category" value="{{ $event->category }}">
                <input type="hidden" name="audience" value="{{ $event->audience }}">
                <input type="hidden" name="location_name" value="{{ $event->location_name }}">
                <input type="hidden" name="latitude" value="{{ $event->latitude }}">
                <input type="hidden" name="longitude" value="{{ $event->longitude }}">
                <input type="hidden" name="start_datetime" value="{{ $event->start_datetime->format('Y-m-d\TH:i') }}">
                <input type="hidden" name="end_datetime" value="{{ $event->end_datetime->format('Y-m-d\TH:i') }}">
                <input type="file" id="image-input" name="image" accept="image/*" class="hidden" onchange="document.getElementById('image-upload-form').submit()">
                <div onclick="document.getElementById('image-input').click()"
                     class="cursor-pointer relative group">
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
                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-t-2xl">
                        <span class="bg-white text-gray-800 text-sm font-semibold px-4 py-2 rounded-full shadow">📷 Click to change image</span>
                    </div>
                </div>
            </form>
        @else
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
                    {{-- Organizer / Admin management buttons --}}
                    @if($canManage)
                        <a href="{{ route('organizer.events.edit', $event->id) }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-medium text-sm hover:bg-blue-700 transition-colors">
                            ✏️ Edit Event
                        </a>

                        @if($event->is_archived)
                            <form method="POST" action="{{ route('organizer.events.unarchive', $event->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-50 text-green-700 border border-green-200 rounded-xl font-medium text-sm hover:bg-green-100 transition-colors">
                                    📂 Restore Event
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('organizer.events.archive', $event->id) }}"
                                  onsubmit="return confirm('Archive this event? It will be hidden from public view.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-xl font-medium text-sm hover:bg-amber-100 transition-colors">
                                    🗄️ Archive Event
                                </button>
                            </form>
                        @endif

                        @if($event->is_archived)
                            <span class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-500 rounded-xl text-xs font-medium">
                                🗄️ This event is archived
                            </span>
                        @endif

                        <hr class="border-gray-100">
                    @endif

                    @auth
                        {{-- Save / Unsave button --}}
                        @if(!$event->is_archived)
                        <form method="POST" action="{{ route('favorites.toggle', $event->id) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-medium text-sm transition-colors
                                           {{ $isFavorited ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' : 'bg-green-50 text-green-600 border border-green-200 hover:bg-green-100' }}">
                                {{ $isFavorited ? '❤️ Saved' : '🤍 Save Event' }}
                            </button>
                        </form>
                        @endif
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
    const imageUrl = @json($event->image ? asset('storage/' . $event->image) : null);

    let iconHtml;
    if (imageUrl) {
        iconHtml = `<div style="width:40px;height:40px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.35);overflow:hidden;background:#e5e7eb;"><img src="${imageUrl}" style="width:100%;height:100%;object-fit:cover;" /></div>`;
    } else {
        iconHtml = `<div style="font-size:28px;line-height:1;">${emoji}</div>`;
    }

    const icon = L.divIcon({
        html: iconHtml,
        iconSize: [40, 40],
        iconAnchor: [20, 20],
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
