@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Page header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Events in Butuan City</h1>
        <p class="text-gray-500 mt-1">Find what's happening around you</p>
    </div>

    {{-- Search + Filter --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-8 items-start sm:items-center">
        <form method="GET" action="{{ route('events.index') }}" class="flex flex-1 flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search events or places..."
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <select name="category"
                    class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="All" {{ request('category', 'All') === 'All' ? 'selected' : '' }}>All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                        {{ \App\Models\Event::CATEGORIES[$cat] }} {{ $cat }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                Search
            </button>
            @if(request('search') || (request('category') && request('category') !== 'All'))
                <a href="{{ route('events.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2.5 px-2">Clear</a>
            @endif
        </form>

        {{-- Archive tab: only visible to admin and organizer, sits between categories and search --}}
        @if($isPrivileged)
            <a href="{{ $showArchived ? route('events.index') : route('events.index', ['filter' => 'archived']) }}"
               class="shrink-0 flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors border
                      {{ $showArchived ? 'bg-gray-700 text-white border-gray-700' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-gray-800' }}">
                🗄️ Archive
            </a>
        @endif
    </div>

    {{-- Category tabs --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('events.index') }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                  {{ (!request('category') || request('category') === 'All') && !$showArchived ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
            All
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('events.index', ['category' => $cat, 'search' => request('search')]) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                      {{ request('category') === $cat && !$showArchived ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                {{ \App\Models\Event::CATEGORIES[$cat] }} {{ $cat }}
            </a>
        @endforeach

    </div>

    {{-- Results count --}}
    <p class="text-sm text-gray-500 mb-6">
        {{ $events->total() }} event{{ $events->total() !== 1 ? 's' : '' }} found
        @if($showArchived)
            <span class="ml-2 inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full">🗄️ Archived events — not visible to the public</span>
        @endif
    </p>

    {{-- Events grid --}}
    @if($events->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-10">
            @foreach($events as $event)
                @include('partials.event-card', ['event' => $event])
            @endforeach
        </div>

        {{-- Pagination --}}
        {{ $events->links() }}
    @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <p class="text-5xl mb-4">🔍</p>
            <p class="text-xl font-semibold text-gray-700 mb-2">No events found</p>
            <p class="text-gray-500 text-sm">Try a different search or category</p>
            <a href="{{ route('events.index') }}" class="mt-4 inline-block text-blue-600 text-sm font-medium hover:underline">
                Clear filters
            </a>
        </div>
    @endif

</div>
@endsection
