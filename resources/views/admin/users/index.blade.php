@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">👥 Manage Users</h1>
            <p class="text-gray-500 text-sm mt-1">View users and manage their roles</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-blue-600">← Dashboard</a>
    </div>

    {{-- Role filter --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('admin.users.index') }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                  {{ !request('role') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All
        </a>
        @foreach(['user', 'organizer', 'admin'] as $role)
            <a href="{{ route('admin.users.index', ['role' => $role]) }}"
               class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                      {{ request('role') === $role ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ ucfirst($role) }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($users->isNotEmpty())
            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Name</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Email</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Phone</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Role</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Joined</th>
                            <th class="text-left py-3 px-5 text-xs font-semibold text-gray-500 uppercase">Change Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-gray-600">{{ $user->email }}</td>
                                <td class="py-4 px-5 text-gray-500">{{ $user->phone_number ?? '—' }}</td>
                                <td class="py-4 px-5">
                                    @if($user->role === 'admin')
                                        <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2.5 py-1 rounded-full">⚙️ Admin</span>
                                    @elseif($user->role === 'organizer')
                                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">📋 Organizer</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2.5 py-1 rounded-full">👤 User</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="py-4 px-5">
                                    @if(!$user->isAdmin())
                                        <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" class="text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>Organizer</option>
                                            </select>
                                            <button type="submit" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition-colors font-medium">Save</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400">Admin (protected)</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile card list --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($users as $user)
                    <div class="p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                @if($user->phone_number)
                                    <p class="text-xs text-gray-400">{{ $user->phone_number }}</p>
                                @endif
                            </div>
                            <div class="shrink-0">
                                @if($user->role === 'admin')
                                    <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-2 py-0.5 rounded-full">⚙️ Admin</span>
                                @elseif($user->role === 'organizer')
                                    <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded-full">📋 Organizer</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-2 py-0.5 rounded-full">👤 User</span>
                                @endif
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mb-2">Joined {{ $user->created_at->format('M d, Y') }}</p>
                        @if(!$user->isAdmin())
                            <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="role" class="flex-1 text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                    <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>Organizer</option>
                                </select>
                                <button type="submit" class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg font-medium">Save</button>
                            </form>
                        @else
                            <p class="text-xs text-gray-400 italic">Admin role is protected</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="p-5">{{ $users->links() }}</div>
        @else
            <div class="text-center py-16">
                <p class="text-gray-400 text-sm">No users found.</p>
            </div>
        @endif
    </div>
</div>
@endsection
