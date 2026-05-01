@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">⚙️ Admin Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">AliButuan control center</p>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['totalUsers'] }}</p>
            <p class="text-sm text-gray-500 mt-1">👤 Users</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-purple-600">{{ $stats['totalOrganizers'] }}</p>
            <p class="text-sm text-gray-500 mt-1">📋 Organizers</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $stats['totalEvents'] }}</p>
            <p class="text-sm text-gray-500 mt-1">📅 Total Events</p>
        </div>
        <div class="bg-white rounded-2xl border border-yellow-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pendingEvents'] }}</p>
            <p class="text-sm text-gray-500 mt-1">⏳ Pending</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-100 shadow-sm p-5 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $stats['approvedEvents'] }}</p>
            <p class="text-sm text-gray-500 mt-1">✅ Approved</p>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
        <a href="{{ route('admin.events.index', ['status' => 'pending']) }}"
           class="flex items-center gap-4 bg-yellow-50 border border-yellow-200 rounded-2xl p-5 hover:bg-yellow-100 transition-colors">
            <span class="text-3xl">⏳</span>
            <div>
                <p class="font-semibold text-yellow-800">Review Pending Events</p>
                <p class="text-sm text-yellow-600">{{ $stats['pendingEvents'] }} event{{ $stats['pendingEvents'] !== 1 ? 's' : '' }} waiting for approval</p>
            </div>
            <span class="ml-auto text-yellow-500">→</span>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-4 bg-blue-50 border border-blue-200 rounded-2xl p-5 hover:bg-blue-100 transition-colors">
            <span class="text-3xl">👥</span>
            <div>
                <p class="font-semibold text-blue-800">Manage Users</p>
                <p class="text-sm text-blue-600">View and manage user roles</p>
            </div>
            <span class="ml-auto text-blue-500">→</span>
        </a>
    </div>

    {{-- Recent events --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Recent Event Submissions</h2>
            <a href="{{ route('admin.events.index') }}" class="text-sm text-blue-600 hover:underline">View all →</a>
        </div>

        @if($recentEvents->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Event</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Organizer</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Date</th>
                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($recentEvents as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium text-gray-800">{{ Str::limit($event->title, 40) }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $event->organizer->name }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ $event->created_at->format('M d, Y') }}</td>
                                <td class="py-3 px-4">
                                    @if($event->status === 'approved')
                                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">Approved</span>
                                    @elseif($event->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-700 text-xs font-semibold px-2.5 py-1 rounded-full">Pending</span>
                                    @else
                                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-2.5 py-1 rounded-full">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-400 text-sm text-center py-8">No events yet.</p>
        @endif
    </div>
</div>
@endsection
