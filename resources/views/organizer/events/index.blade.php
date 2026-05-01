@extends('layouts.app')

@section('title', 'My Events')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-800">📋 My Submitted Events</h1>
        <a href="{{ route('organizer.events.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
            + New Event
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($events->isNotEmpty())
            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Title</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-5 font-medium text-gray-800">{{ Str::limit($event->title, 45) }}</td>
                                <td class="py-4 px-5 text-gray-600">{{ $event->category_emoji }} {{ $event->category }}</td>
                                <td class="py-4 px-5 text-gray-500">{{ $event->start_datetime->format('M d, Y') }}</td>
                                <td class="py-4 px-5">
                                    @if($event->status === 'approved')
                                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">✅ Approved</span>
                                    @elseif($event->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">⏳ Pending</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">❌ Rejected</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        @if($event->status === 'approved')
                                            <a href="{{ route('events.show', $event->id) }}" class="text-blue-600 hover:underline text-xs font-medium">View</a>
                                        @else
                                            <a href="{{ route('organizer.events.edit', $event->id) }}" class="text-blue-600 hover:underline text-xs font-medium">Edit</a>
                                            <form method="POST" action="{{ route('organizer.events.destroy', $event->id) }}" onsubmit="return confirm('Delete this event?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile card list --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($events as $event)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm leading-snug">{{ $event->title }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $event->category_emoji }} {{ $event->category }}</p>
                                <p class="text-xs text-gray-400">📅 {{ $event->start_datetime->format('M d, Y') }}</p>
                            </div>
                            <div class="shrink-0">
                                @if($event->status === 'approved')
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">✅ Approved</span>
                                @elseif($event->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full">⏳ Pending</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">❌ Rejected</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2 mt-2">
                            @if($event->status === 'approved')
                                <a href="{{ route('events.show', $event->id) }}" class="bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-lg">View</a>
                            @else
                                <a href="{{ route('organizer.events.edit', $event->id) }}" class="bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-lg">Edit</a>
                                <form method="POST" action="{{ route('organizer.events.destroy', $event->id) }}" onsubmit="return confirm('Delete this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-gray-100 text-red-500 text-xs font-semibold px-3 py-1.5 rounded-lg">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-5">{{ $events->links() }}</div>
        @else
            <div class="text-center py-16">
                <p class="text-4xl mb-3">📭</p>
                <p class="text-gray-500 text-sm">You haven't submitted any events yet.</p>
                <a href="{{ route('organizer.events.create') }}" class="mt-3 inline-block text-blue-600 text-sm font-medium hover:underline">
                    Submit your first event →
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
