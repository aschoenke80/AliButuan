@extends('layouts.app')

@section('title', 'Submit New Event')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <a href="{{ route('organizer.events.index') }}" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">← Back</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">📝 Submit a New Event</h1>
        <p class="text-gray-500 text-sm mt-1">Events will be visible after admin approval.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('organizer.events.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 space-y-6">
        @csrf

        {{-- Title & Category --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Event Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g. Kadayawan Festival 2026" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">— Select category —</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                            {{ \App\Models\Event::CATEGORIES[$cat] }} {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
            <textarea name="description" rows="5" required
                      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-y"
                      placeholder="Describe your event...">{{ old('description') }}</textarea>
        </div>

        {{-- Audience --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Target Audience <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="audience" value="{{ old('audience') }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="e.g. All Ages, Students, Adults, Families" />
        </div>

        {{-- Location name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Location Name <span class="text-red-500">*</span></label>
            <input type="text" name="location_name" value="{{ old('location_name') }}" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="e.g. Robinsons Place Butuan, Agusan River" />
        </div>

        {{-- Map picker for coordinates --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Event Location on Map <span class="text-red-500">*</span>
                <span class="text-gray-400 font-normal text-xs ml-1">(click the map to place a pin)</span>
            </label>
            <div id="picker-map" class="h-56 rounded-xl border border-gray-300 mb-3 z-0"></div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Latitude</label>
                    <input type="number" name="latitude" id="lat-input" step="any"
                           value="{{ old('latitude', 8.9475) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Longitude</label>
                    <input type="number" name="longitude" id="lng-input" step="any"
                           value="{{ old('longitude', 125.5406) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
            </div>
        </div>

        {{-- Date & Time --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="start_datetime" value="{{ old('start_datetime') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="end_datetime" value="{{ old('end_datetime') }}" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
        </div>

        {{-- Image upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Event Image <span class="text-gray-400">(optional, max 2MB)</span></label>
            <input type="file" name="image" accept="image/*"
                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100 file:transition-colors" />
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-2.5 rounded-xl transition-colors text-sm">
                Submit for Approval
            </button>
            <a href="{{ route('organizer.events.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2.5 px-4">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const initLat = parseFloat(document.getElementById('lat-input').value) || 8.9475;
    const initLng = parseFloat(document.getElementById('lng-input').value) || 125.5406;

    const BUTUAN_BOUNDS = L.latLngBounds(
        L.latLng(8.88, 125.47),
        L.latLng(9.02, 125.62)
    );
    const pickerMap = L.map('picker-map', {
        maxBounds: BUTUAN_BOUNDS,
        maxBoundsViscosity: 1.0,
        minZoom: 12,
    }).setView([initLat, initLng], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors © <a href="https://carto.com">CARTO</a>'
    }).addTo(pickerMap);

    let pin = L.marker([initLat, initLng], { draggable: true }).addTo(pickerMap);
    pin.bindPopup('Drag me to the event location').openPopup();

    function updateInputs(lat, lng) {
        document.getElementById('lat-input').value = lat.toFixed(7);
        document.getElementById('lng-input').value = lng.toFixed(7);
    }

    // Click on map to move pin
    pickerMap.on('click', function(e) {
        pin.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
    });

    // Drag pin to update inputs
    pin.on('dragend', function() {
        const pos = pin.getLatLng();
        updateInputs(pos.lat, pos.lng);
    });
</script>
@endpush
