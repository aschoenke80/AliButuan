@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
@php
    $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
    $roleColor = match($user->role) {
        'admin'     => 'bg-purple-600',
        'organizer' => 'bg-blue-600',
        default     => 'bg-green-600',
    };
    $roleBadge = match($user->role) {
        'admin'     => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'label' => '⚙️ Administrator'],
        'organizer' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'label' => '🎪 Organizer'],
        default     => ['bg' => 'bg-green-100',  'text' => 'text-green-700',  'label' => '🙋 Attendee'],
    };
    $hasErrors = $errors->any();
@endphp

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Avatar + name header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-full {{ $roleColor }} flex items-center justify-center text-white text-2xl font-bold shrink-0 select-none">
                {{ $initials }}
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-bold text-gray-900 truncate">{{ $user->name }}</h1>
                <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleBadge['bg'] }} {{ $roleBadge['text'] }} mt-1">
                    {{ $roleBadge['label'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- Profile Info Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Profile Information</h2>
            <button type="button" id="edit-toggle"
                    onclick="toggleEdit()"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                <svg id="edit-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span id="edit-label">Edit</span>
            </button>
        </div>

        {{-- Read-only view --}}
        <div id="view-mode" class="{{ $hasErrors ? 'hidden' : '' }} space-y-4">
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-500">Full Name</span>
                <span class="text-sm font-medium text-gray-900">{{ $user->name }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
                <span class="text-sm text-gray-500">Email Address</span>
                <span class="text-sm font-medium text-gray-900">{{ $user->email }}</span>
            </div>
            <div class="flex justify-between py-3">
                <span class="text-sm text-gray-500">Phone Number</span>
                <span class="text-sm font-medium text-gray-900">{{ $user->phone_number ?: '—' }}</span>
            </div>
        </div>

        {{-- Edit form --}}
        <div id="edit-mode" class="{{ $hasErrors ? '' : 'hidden' }}">
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

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                        Save Changes
                    </button>
                    <button type="button" onclick="toggleEdit()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Change Password Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-semibold text-gray-800">Change Password</h2>
            <button type="button" onclick="togglePassword()"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span id="pwd-label">Change</span>
            </button>
        </div>

        <div id="pwd-mode" class="hidden">
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

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                        Update Password
                    </button>
                    <button type="button" onclick="togglePassword()"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <p id="pwd-hint" class="text-sm text-gray-400">Click "Change" to update your password.</p>
    </div>

</div>

<script>
function toggleEdit() {
    const view = document.getElementById('view-mode');
    const edit = document.getElementById('edit-mode');
    const label = document.getElementById('edit-label');
    const isHidden = edit.classList.contains('hidden');
    edit.classList.toggle('hidden', !isHidden);
    view.classList.toggle('hidden', isHidden);
    label.textContent = isHidden ? 'Cancel' : 'Edit';
}
function togglePassword() {
    const form = document.getElementById('pwd-mode');
    const hint = document.getElementById('pwd-hint');
    const label = document.getElementById('pwd-label');
    const isHidden = form.classList.contains('hidden');
    form.classList.toggle('hidden', !isHidden);
    hint.classList.toggle('hidden', isHidden);
    label.textContent = isHidden ? 'Cancel' : 'Change';
}
</script>
@endsection
