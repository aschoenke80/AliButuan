@extends('layouts.app')

@section('title', 'Event Map')

@section('content')
{{-- Mobile: map on top, events drawer toggled below. Desktop: side-by-side. --}}
<div class="flex flex-col md:flex-row" style="height: calc(100vh - 4rem);">

    {{-- ── SIDEBAR (desktop left panel / mobile hidden initially) ─────── --}}
    <aside id="map-sidebar"
           class="hidden md:flex w-full md:w-80 bg-white border-r border-gray-100 flex-col shrink-0 z-10">
        <div class="p-4 border-b border-gray-100">
            <h1 class="text-lg font-bold text-gray-800">🗺️ Event Map</h1>
            <p class="text-xs text-gray-500 mt-0.5">Butuan City, Philippines</p>
        </div>

        {{-- Category filter --}}
        <div class="p-4 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Filter by Category</p>
            <div class="flex flex-wrap gap-1.5">
                <button onclick="filterCategory('all')"
                        class="category-btn active-btn px-3 py-1 rounded-full text-xs font-medium border transition-colors" data-cat="all">
                    All
                </button>
                @foreach($categories as $cat)
                    <button onclick="filterCategory('{{ $cat }}')"
                            class="category-btn px-3 py-1 rounded-full text-xs font-medium border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 transition-colors"
                            data-cat="{{ $cat }}">
                        {{ \App\Models\Event::CATEGORIES[$cat] }} {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Event list --}}
        <div class="flex-1 overflow-y-auto p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Events</p>
            <div id="event-list-desktop" class="space-y-2">
                <p class="text-xs text-gray-400">Loading events...</p>
            </div>
        </div>
    </aside>

    {{-- ── MAP ───────────────────────────────────────────────────────── --}}
    <div class="flex-1 relative" style="min-height: 55vw;">
        <div id="map" class="w-full h-full z-0"></div>

        {{-- Mobile: floating toggle button --}}
        <button id="events-drawer-btn"
                onclick="toggleDrawer()"
                class="md:hidden absolute bottom-4 left-1/2 -translate-x-1/2 z-20
                       bg-blue-600 text-white text-sm font-semibold px-5 py-2.5 rounded-full shadow-lg
                       flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <span id="drawer-btn-label">Show Events</span>
        </button>
    </div>

    {{-- ── MOBILE EVENTS DRAWER ───────────────────────────────────────── --}}
    <div id="mobile-drawer"
         class="md:hidden fixed bottom-0 left-0 right-0 bg-white rounded-t-2xl shadow-2xl z-30
                translate-y-full transition-transform duration-300 ease-in-out"
         style="max-height: 65vh;">
        {{-- Drawer handle --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-bold text-gray-800">📍 Events</span>
            <button onclick="toggleDrawer()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        {{-- Category pills --}}
        <div class="px-4 py-2.5 border-b border-gray-100 overflow-x-auto">
            <div class="flex gap-1.5 whitespace-nowrap">
                <button onclick="filterCategory('all')"
                        class="mobile-cat-btn active-mobile-btn px-3 py-1 rounded-full text-xs font-medium border transition-colors" data-cat="all">
                    All
                </button>
                @foreach($categories as $cat)
                    <button onclick="filterCategory('{{ $cat }}')"
                            class="mobile-cat-btn px-3 py-1 rounded-full text-xs font-medium border border-gray-200 text-gray-600"
                            data-cat="{{ $cat }}">
                        {{ \App\Models\Event::CATEGORIES[$cat] }} {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Event list --}}
        <div class="overflow-y-auto p-4 space-y-2" style="max-height: calc(65vh - 8rem);">
            <div id="event-list-mobile">
                <p class="text-xs text-gray-400">Loading events...</p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Active category button */
        .active-btn { background-color: #2563eb; color: white; border-color: #2563eb; }
        .active-mobile-btn { background-color: #2563eb; color: white; border-color: #2563eb; }
        /* Leaflet popup styling */
        .leaflet-popup-content-wrapper { border-radius: 12px; }
    </style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Butuan City center coordinates and bounds
    const BUTUAN_CENTER = [8.9475, 125.5406];
    const BUTUAN_BOUNDS = L.latLngBounds(
        L.latLng(8.88, 125.47),   // SW corner
        L.latLng(9.02, 125.62)    // NE corner
    );

    // Initialize the map restricted to Butuan City
    const map = L.map('map', {
        maxBounds: BUTUAN_BOUNDS,
        maxBoundsViscosity: 1.0,
        minZoom: 12,
    }).setView(BUTUAN_CENTER, 13);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors © <a href="https://carto.com">CARTO</a>',
        maxZoom: 19,
    }).addTo(map);

    let allEvents  = [];
    let markers    = [];
    let activeCategory = 'all';
    let drawerOpen = false;

    // Mobile drawer toggle
    function toggleDrawer() {
        drawerOpen = !drawerOpen;
        const drawer = document.getElementById('mobile-drawer');
        const label  = document.getElementById('drawer-btn-label');
        if (drawerOpen) {
            drawer.style.transform = 'translateY(0)';
            label.textContent = 'Hide Events';
        } else {
            drawer.style.transform = 'translateY(100%)';
            label.textContent = 'Show Events';
        }
    }

    // Create a marker — uses event image if available, otherwise emoji
    function createMarker(event) {
        let iconHtml;
        if (event.image_url) {
            iconHtml = `<div style="width:40px;height:40px;border-radius:50%;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.35);overflow:hidden;background:#e5e7eb;">
                <img src="${event.image_url}" style="width:100%;height:100%;object-fit:cover;" />
            </div>`;
        } else {
            iconHtml = `<div style="font-size:28px;line-height:1;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.3));">${event.emoji}</div>`;
        }

        const icon = L.divIcon({
            html: iconHtml,
            iconSize: [40, 40],
            iconAnchor: [20, 20],
            className: '',
        });

        const popupImage = event.image_url
            ? `<img src="${event.image_url}" style="width:100%;height:80px;object-fit:cover;border-radius:6px;margin-bottom:8px;" />`
            : '';

        const marker = L.marker([event.latitude, event.longitude], { icon })
            .addTo(map)
            .bindPopup(`
                <div style="min-width:180px;">
                    ${popupImage}
                    <p style="font-size:11px;color:#6b7280;margin-bottom:4px;">${event.emoji} ${event.category}</p>
                    <p style="font-weight:700;font-size:14px;margin-bottom:6px;color:#1f2937;">${event.title}</p>
                    <p style="font-size:12px;color:#6b7280;margin-bottom:2px;">📅 ${event.start_date}</p>
                    <p style="font-size:12px;color:#6b7280;margin-bottom:10px;">📍 ${event.location_name}</p>
                    <a href="${event.url}"
                       style="display:inline-block;background:#2563eb;color:white;padding:5px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;">
                        View Details →
                    </a>
                </div>
            `);

        marker.eventData = event;
        return marker;
    }

    // Build event card HTML
    function eventCardHTML(e) {
        return `
            <button onclick="focusEvent(${e.id})"
                    class="w-full text-left bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-xl p-3 transition-colors">
                <p class="text-xs text-gray-400 mb-0.5">${e.emoji} ${e.category}</p>
                <p class="text-sm font-semibold text-gray-800 leading-snug">${e.title}</p>
                <p class="text-xs text-gray-500 mt-1">📅 ${e.start_date}</p>
                <p class="text-xs text-gray-500">📍 ${e.location_name}</p>
            </button>`;
    }

    // Build the sidebar event lists (desktop + mobile)
    function buildEventList(events) {
        const empty = '<p class="text-xs text-gray-400 text-center py-4">No events in this category.</p>';
        const html  = events.length === 0 ? empty : events.map(eventCardHTML).join('');
        document.getElementById('event-list-desktop').innerHTML = html;
        document.getElementById('event-list-mobile').innerHTML  = html;
    }

    // Focus the map on a specific event's marker
    function focusEvent(eventId) {
        const marker = markers.find(m => m.eventData.id === eventId);
        if (marker) {
            map.setView(marker.getLatLng(), 16);
            marker.openPopup();
            // Close drawer on mobile after selecting an event
            if (drawerOpen) toggleDrawer();
        }
    }

    // Remove all markers from the map
    function clearMarkers() {
        markers.forEach(m => map.removeLayer(m));
        markers = [];
    }

    // Show markers for the active category
    function renderMarkers(events) {
        clearMarkers();
        events.forEach(event => {
            markers.push(createMarker(event));
        });
    }

    // Filter by category
    function filterCategory(cat) {
        activeCategory = cat;

        // Update desktop button styles
        document.querySelectorAll('.category-btn').forEach(btn => {
            if (btn.dataset.cat === cat) {
                btn.classList.add('active-btn');
                btn.classList.remove('border-gray-200', 'text-gray-600');
            } else {
                btn.classList.remove('active-btn');
                btn.classList.add('border-gray-200', 'text-gray-600');
            }
        });

        // Update mobile button styles
        document.querySelectorAll('.mobile-cat-btn').forEach(btn => {
            if (btn.dataset.cat === cat) {
                btn.classList.add('active-mobile-btn');
                btn.classList.remove('border-gray-200', 'text-gray-600');
            } else {
                btn.classList.remove('active-mobile-btn');
                btn.classList.add('border-gray-200', 'text-gray-600');
            }
        });

        const filtered = cat === 'all' ? allEvents : allEvents.filter(e => e.category === cat);
        renderMarkers(filtered);
        buildEventList(filtered);
    }

    // Fetch events from the API and populate the map
    fetch('{{ route("map.events") }}')
        .then(res => res.json())
        .then(events => {
            allEvents = events;
            renderMarkers(events);
            buildEventList(events);

            // If URL has lat/lng (coming from event detail page), center on that event
            const params = new URLSearchParams(window.location.search);
            if (params.get('lat') && params.get('lng')) {
                map.setView([parseFloat(params.get('lat')), parseFloat(params.get('lng'))], 16);
            }
        })
        .catch(() => {
            const msg = '<p class="text-xs text-red-400">Failed to load events.</p>';
            document.getElementById('event-list-desktop').innerHTML = msg;
            document.getElementById('event-list-mobile').innerHTML  = msg;
        });

    // Show sidebar on desktop
    if (window.innerWidth >= 768) {
        document.getElementById('map-sidebar').classList.remove('hidden');
        document.getElementById('map-sidebar').classList.add('flex');
    }
    window.addEventListener('resize', () => {
        const sidebar = document.getElementById('map-sidebar');
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('flex');
        } else {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
        }
    });
</script>
@endpush
