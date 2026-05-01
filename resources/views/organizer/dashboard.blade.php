@extends('layouts.app')

@section('title', 'Organizer Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📋 Organizer Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <a href="{{ route('organizer.events.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
            + Submit Event
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Submitted</p>
        </div>
        <div class="bg-white rounded-2xl border border-yellow-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Pending Review</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Approved</p>
        </div>
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Rejected</p>
        </div>
    </div>

    {{-- Recent Events --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Recent Events</h2>
            <a href="{{ route('organizer.events.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
        </div>

        @if($recentEvents->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Event</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentEvents as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-800">{{ Str::limit($event->title, 40) }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $event->category_emoji }} {{ $event->category }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $event->start_datetime->format('M d, Y') }}</td>
                                <td class="py-3 px-4">
                                    @if($event->status === 'approved')
                                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">Approved</span>
                                    @elseif($event->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">Pending</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">Rejected</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if($event->status !== 'approved')
                                        <a href="{{ route('organizer.events.edit', $event->id) }}"
                                           class="text-blue-600 hover:underline text-xs font-medium">Edit</a>
                                    @else
                                        <a href="{{ route('events.show', $event->id) }}"
                                           class="text-gray-500 hover:text-gray-700 text-xs font-medium">View</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-10">
                <p class="text-gray-400 text-sm">No events submitted yet.</p>
                <a href="{{ route('organizer.events.create') }}" class="mt-2 inline-block text-blue-600 text-sm font-medium hover:underline">
                    Submit your first event →
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
