@extends('layouts.app')

@section('title', 'Manage Events')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">📅 Manage Events</h1>
            <p class="text-gray-500 text-sm mt-1">Approve, reject, feature, or remove events</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-blue-600">← Dashboard</a>
    </div>

    {{-- Status filter tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.events.index') }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                  {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All
        </a>
        @foreach(['pending', 'approved', 'rejected'] as $status)
            <a href="{{ route('admin.events.index', ['status' => $status]) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                      {{ request('status') === $status ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ ucfirst($status) }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($events->isNotEmpty())
            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Event</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Organizer</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Category</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Featured</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-5">
                                    <p class="font-medium text-gray-800">{{ Str::limit($event->title, 35) }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">📍 {{ Str::limit($event->location_name, 25) }}</p>
                                </td>
                                <td class="py-4 px-5 text-gray-600">{{ $event->organizer->name }}</td>
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
                                    <form method="POST" action="{{ route('admin.events.feature', $event->id) }}">
                                        @csrf
                                        <button type="submit" class="text-lg" title="{{ $event->is_featured ? 'Unfeature' : 'Feature' }}">
                                            {{ $event->is_featured ? '⭐' : '☆' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($event->status === 'pending')
                                            <form method="POST" action="{{ route('admin.events.approve', $event->id) }}">
                                                @csrf
                                                <button type="submit" class="bg-green-100 hover:bg-green-200 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.events.reject', $event->id) }}">
                                                @csrf
                                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">Reject</button>
                                            </form>
                                        @endif
                                        @if($event->status === 'approved')
                                            <a href="{{ route('events.show', $event->id) }}" class="text-blue-600 hover:underline text-xs font-medium">View</a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('Permanently delete this event?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline text-xs font-medium">Delete</button>
                                        </form>
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
                                <p class="text-xs text-gray-500 mt-0.5">{{ $event->category_emoji }} {{ $event->category }} · {{ $event->start_datetime->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">by {{ $event->organizer->name }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1.5 shrink-0">
                                @if($event->status === 'approved')
                                    <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">✅ Approved</span>
                                @elseif($event->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2 py-0.5 rounded-full">⏳ Pending</span>
                                @else
                                    <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">❌ Rejected</span>
                                @endif
                                <form method="POST" action="{{ route('admin.events.feature', $event->id) }}">
                                    @csrf
                                    <button type="submit" class="text-base" title="{{ $event->is_featured ? 'Unfeature' : 'Feature' }}">{{ $event->is_featured ? '⭐' : '☆' }}</button>
                                </form>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap mt-2">
                            @if($event->status === 'pending')
                                <form method="POST" action="{{ route('admin.events.approve', $event->id) }}">
                                    @csrf
                                    <button type="submit" class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1.5 rounded-lg">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.events.reject', $event->id) }}">
                                    @csrf
                                    <button type="submit" class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg">Reject</button>
                                </form>
                            @endif
                            @if($event->status === 'approved')
                                <a href="{{ route('events.show', $event->id) }}" class="bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-lg">View</a>
                            @endif
                            <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" onsubmit="return confirm('Permanently delete this event?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-gray-100 text-red-500 text-xs font-semibold px-3 py-1.5 rounded-lg">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-5">{{ $events->links() }}</div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No events found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
