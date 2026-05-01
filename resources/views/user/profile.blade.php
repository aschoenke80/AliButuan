@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Role-aware header --}}
    @php
        $roleColor = match($user->role) {
            'admin'     => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'ring' => 'ring-purple-300', 'avatar' => 'bg-purple-600'],
            'organizer' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'ring' => 'ring-blue-300',   'avatar' => 'bg-blue-600'],
            default     => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'ring' => 'ring-green-300',  'avatar' => 'bg-green-600'],
        };
        $roleLabel = match($user->role) {
            'admin'     => '⚙️ Administrator',
            'organizer' => '🎪 Event Organizer',
            default     => '🙋 Attendee',
        };
        $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
    @endphp

    <div class="flex items-center gap-5 mb-8">
        <div class="w-16 h-16 rounded-full {{ $roleColor['avatar'] }} flex items-center justify-center text-white text-2xl font-bold ring-4 {{ $roleColor['ring'] }} shrink-0">
            {{ $initials }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $roleColor['bg'] }} {{ $roleColor['text'] }} mt-1">
                {{ $roleLabel }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">

        {{-- Role-specific stats --}}
        @if($user->role === 'admin')
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $stats['total_users'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Total Users</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-purple-600">{{ $stats['total_events'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Total Events</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-yellow-500">{{ $stats['pending_events'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Pending</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-green-500">{{ $stats['approved_events'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Approved</div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4 flex flex-wrap gap-3">
            <a href="{{ route('admin.users.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                👥 Manage Users
            </a>
            <a href="{{ route('admin.events.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                📋 Manage Events
            </a>
        </div>

        @elseif($user->role === 'organizer')
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-blue-600">{{ $stats['my_events'] }}</div>
                <div class="text-xs text-gray-500 mt-1">My Events</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-green-500">{{ $stats['approved'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Approved</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-yellow-500">{{ $stats['pending'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Pending</div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
                <div class="text-3xl font-bold text-red-500">{{ $stats['rejected'] }}</div>
                <div class="text-xs text-gray-500 mt-1">Rejected</div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex flex-wrap gap-3">
            <a href="{{ route('organizer.events.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                📋 My Events
            </a>
            <a href="{{ route('organizer.events.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                ➕ Create Event
            </a>
        </div>

        @else
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('user.favorites') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center hover:border-green-300 transition-colors group">
                <div class="text-3xl font-bold text-green-500 group-hover:scale-110 transition-transform">{{ $stats['favorites'] }}</div>
                <div class="text-xs text-gray-500 mt-1">❤️ Saved Events</div>
            </a>
            <a href="{{ route('user.notifications') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center hover:border-green-300 transition-colors group">
                <div class="text-3xl font-bold text-green-500 group-hover:scale-110 transition-transform">{{ $stats['notifications'] }}</div>
                <div class="text-xs text-gray-500 mt-1">🔔 Unread Alerts</div>
            </a>
        </div>
        @endif

        {{-- Profile Info Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-5">Profile Information</h2>

            <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-gray-400">(optional)</span></label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="+63 900 000 0000" />
                </div>

                <div class="pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Change Password Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-5">Change Password</h2>

            <form method="POST" action="{{ route('user.password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                    Update Password
                </button>
            </form>
        </div>

    </div>
</div>
@endsection

