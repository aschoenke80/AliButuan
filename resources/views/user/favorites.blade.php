@extends('layouts.app')

@section('title', 'My Favorites')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <h1 class="text-2xl font-bold text-gray-800 mb-2">❤️ My Saved Events</h1>
    <p class="text-gray-500 text-sm mb-8">Events you've saved for later</p>

    @if($favorites->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($favorites as $fav)
                @if($fav->event)
                    @include('partials.event-card', ['event' => $fav->event])
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <p class="text-5xl mb-4">🤍</p>
            <p class="text-xl font-semibold text-gray-700 mb-2">No saved events yet</p>
            <p class="text-gray-500 text-sm mb-6">Browse events and click "Save Event" to add them here</p>
            <a href="{{ route('events.index') }}" class="inline-block bg-blue-600 text-white text-sm font-medium px-6 py-2.5 rounded-xl hover:bg-blue-700 transition-colors">
                Browse Events
            </a>
        </div>
    @endif
</div>
@endsection
