@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Create your account</h1>
    <p class="text-gray-500 text-sm mb-6">Join AliButuan and discover local events</p>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Juan Dela Cruz" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="you@example.com" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="+63 900 000 0000" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="At least 8 characters" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="••••••••" />
        </div>

        {{-- Role selection --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">I am registering as a...</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="user" class="peer sr-only"
                           {{ old('role', 'user') === 'user' ? 'checked' : '' }} />
                    <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-lg p-3 text-center transition-colors">
                        <div class="text-2xl mb-1">👤</div>
                        <p class="text-sm font-medium text-gray-700">Resident / Visitor</p>
                        <p class="text-xs text-gray-400">Discover events</p>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="role" value="organizer" class="peer sr-only"
                           {{ old('role') === 'organizer' ? 'checked' : '' }} />
                    <div class="border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 rounded-lg p-3 text-center transition-colors">
                        <div class="text-2xl mb-1">📋</div>
                        <p class="text-sm font-medium text-gray-700">Event Organizer</p>
                        <p class="text-xs text-gray-400">Submit events</p>
                    </div>
                </label>
            </div>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm">
            Create Account
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline">Sign in</a>
    </p>
@endsection
