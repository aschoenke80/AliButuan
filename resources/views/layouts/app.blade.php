<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'AliButuan') — Butuan City Events</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    {{-- ── NAVBAR ─────────────────────────────────────────────────────── --}}
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <span class="text-2xl">🗺️</span>
                    <span class="font-bold text-xl text-blue-600">AliButuan</span>
                </a>

                {{-- Center Nav (hidden on mobile) --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('home') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                        Home
                    </a>
                    <a href="{{ route('events.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('events.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                        Events
                    </a>
                    <a href="{{ route('map.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                              {{ request()->routeIs('map.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:text-blue-600 hover:bg-gray-50' }}">
                        Map
                    </a>

                    {{-- Categories Dropdown --}}
                    <div class="relative group">
                        <button class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors flex items-center gap-1">
                            Categories
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="absolute top-full left-0 mt-1 w-48 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                            @foreach (\App\Models\Event::CATEGORIES as $cat => $emoji)
                                <a href="{{ route('events.index', ['category' => $cat]) }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 first:rounded-t-xl last:rounded-b-xl">
                                    <span>{{ $emoji }}</span> {{ $cat }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right Side --}}
                <div class="flex items-center gap-2">

                    {{-- Search Icon (links to events page with focus) --}}
                    <a href="{{ route('events.index') }}"
                       class="p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-colors"
                       title="Search Events">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </a>

                    @auth
                        {{-- Notification Bell --}}
                        <a href="{{ route('notifications.index') }}"
                           class="relative p-2 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-gray-50 transition-colors"
                           title="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($unreadNotifications > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center leading-none">
                                    {{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}
                                </span>
                            @endif
                        </a>

                        {{-- Profile Dropdown --}}
                        <div class="relative group">
                            <button class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <div class="w-7 h-7 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:block max-w-24 truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="absolute right-0 top-full mt-1 w-52 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                                </div>
                                <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    👤 My Profile
                                </a>
                                <a href="{{ route('favorites.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    ❤️ My Favorites
                                </a>
                                @if(auth()->user()->isOrganizer())
                                    <a href="{{ route('organizer.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        📋 Organizer Dashboard
                                    </a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        ⚙️ Admin Dashboard
                                    </a>
                                @endif
                                <div class="border-t border-gray-100 mt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-b-xl">
                                            🚪 Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-blue-600 px-3 py-2">Login</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Register
                        </a>
                    @endauth

                    {{-- Mobile menu button --}}
                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

            </div>

            {{-- Mobile Nav (hidden by default) --}}
            <div id="mobile-menu" class="hidden md:hidden pb-3 border-t border-gray-100 mt-2 pt-2">
                <a href="{{ route('home') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">🏠 Home</a>
                <a href="{{ route('events.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">📅 Events</a>
                <a href="{{ route('map.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">🗺️ Map</a>

                <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-2">Categories</p>
                @foreach (\App\Models\Event::CATEGORIES as $cat => $emoji)
                    <a href="{{ route('events.index', ['category' => $cat]) }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        {{ $emoji }} {{ $cat }}
                    </a>
                @endforeach

                @auth
                    <div class="border-t border-gray-100 mt-2 pt-2">
                        <p class="px-4 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</p>
                        <div class="px-4 py-2">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                        </div>
                        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                            <span>🔔 Notifications</span>
                            @if($unreadNotifications > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $unreadNotifications }}</span>
                            @endif
                        </a>
                        <a href="{{ route('user.profile') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">👤 My Profile</a>
                        <a href="{{ route('favorites.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">❤️ My Favorites</a>
                        @if(auth()->user()->isOrganizer())
                            <a href="{{ route('organizer.dashboard') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">📋 Organizer Dashboard</a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">⚙️ Admin Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="px-4 py-1">
                            @csrf
                            <button type="submit" class="w-full text-left py-1.5 text-sm text-red-600 hover:text-red-800">🚪 Logout</button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-gray-100 mt-2 pt-2 px-4 flex gap-2">
                        <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-sm font-medium text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Login</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ── FLASH MESSAGES ─────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">✕</button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>❌ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">✕</button>
            </div>
        </div>
    @endif

    {{-- ── MAIN CONTENT ────────────────────────────────────────────────── --}}
    <main class="flex-1 pb-16 md:pb-0">
        @yield('content')
    </main>

    {{-- ── FOOTER ──────────────────────────────────────────────────────── --}}
    <footer class="bg-white border-t border-gray-100 mt-auto hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">🗺️</span>
                    <span class="font-bold text-blue-600">AliButuan</span>
                    <span class="text-gray-400 text-sm">— Butuan City Event Discovery</span>
                </div>
                <div class="flex items-center gap-6 text-sm text-gray-500">
                    <a href="{{ route('home') }}" class="hover:text-blue-600">Home</a>
                    <a href="{{ route('events.index') }}" class="hover:text-blue-600">Events</a>
                    <a href="{{ route('map.index') }}" class="hover:text-blue-600">Map</a>
                </div>
                <p class="text-xs text-gray-400">© {{ date('Y') }} AliButuan. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- ── MOBILE BOTTOM NAV ──────────────────────────────────────────── --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 flex">
        <a href="{{ route('home') }}"
           class="flex-1 flex flex-col items-center justify-center py-2.5 gap-0.5 text-xs font-medium
                  {{ request()->routeIs('home') ? 'text-blue-600' : 'text-gray-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
            </svg>
            Home
        </a>
        <a href="{{ route('events.index') }}"
           class="flex-1 flex flex-col items-center justify-center py-2.5 gap-0.5 text-xs font-medium
                  {{ request()->routeIs('events.*') ? 'text-blue-600' : 'text-gray-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Events
        </a>
        <a href="{{ route('map.index') }}"
           class="flex-1 flex flex-col items-center justify-center py-2.5 gap-0.5 text-xs font-medium
                  {{ request()->routeIs('map.*') ? 'text-blue-600' : 'text-gray-500' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Map
        </a>
        @auth
            <a href="{{ route('user.profile') }}"
               class="flex-1 flex flex-col items-center justify-center py-2.5 gap-0.5 text-xs font-medium relative
                      {{ request()->routeIs('user.*') ? 'text-blue-600' : 'text-gray-500' }}">
                <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                Profile
                @if($unreadNotifications > 0)
                    <span class="absolute top-1.5 right-4 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </a>
        @else
            <a href="{{ route('login') }}"
               class="flex-1 flex flex-col items-center justify-center py-2.5 gap-0.5 text-xs font-medium text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Login
            </a>
        @endauth
    </nav>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
