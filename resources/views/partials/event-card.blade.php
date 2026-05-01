{{-- Reusable event card. Pass: $event, optional $featured --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow group">

    {{-- Event image or emoji fallback --}}
    <a href="{{ route('events.show', $event->id) }}" class="block relative overflow-hidden h-44">
        @if($event->image)
            <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
        @else
            <div class="w-full h-full bg-gradient-to-br from-blue-50 to-sky-100 flex items-center justify-center text-6xl">
                {{ $event->category_emoji }}
            </div>
        @endif

        {{-- Featured badge --}}
        @if($event->is_featured)
            <span class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-0.5 rounded-full">
                ⭐ Featured
            </span>
        @endif

        {{-- Category badge --}}
        <span class="absolute top-2 right-2 bg-white/90 text-gray-700 text-xs font-medium px-2 py-0.5 rounded-full backdrop-blur-sm">
            {{ $event->category_emoji }} {{ $event->category }}
        </span>
    </a>

    <div class="p-4">
        <a href="{{ route('events.show', $event->id) }}" class="block">
            <h3 class="font-semibold text-gray-800 text-sm leading-snug mb-1 hover:text-blue-600 transition-colors line-clamp-2">
                {{ $event->title }}
            </h3>
        </a>

        <p class="text-xs text-gray-500 flex items-center gap-1 mb-1">
            📅 {{ $event->start_datetime->format('M d, Y • h:i A') }}
        </p>
        <p class="text-xs text-gray-500 flex items-center gap-1">
            📍 {{ $event->location_name }}
        </p>
    </div>
</div>
