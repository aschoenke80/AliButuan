@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🔔 Notifications</h1>
            <p class="text-gray-500 text-sm mt-1">Your latest updates from AliButuan</p>
        </div>
    </div>

    @if($notifications->isNotEmpty())
        <div class="space-y-3">
            @foreach($notifications as $notif)
                <div class="bg-white rounded-xl border {{ $notif->is_read ? 'border-gray-100' : 'border-blue-200 bg-blue-50' }} shadow-sm p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            @if(!$notif->is_read)
                                <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mb-1"></span>
                            @endif
                            <p class="font-semibold text-gray-800 text-sm">{{ $notif->title }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $notif->message }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <p class="text-5xl mb-4">🔔</p>
            <p class="text-xl font-semibold text-gray-700 mb-2">No notifications yet</p>
            <p class="text-gray-500 text-sm">You'll receive notifications when your events are approved or rejected.</p>
        </div>
    @endif

</div>
@endsection
