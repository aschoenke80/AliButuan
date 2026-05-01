<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'AliButuan') — Butuan City Events</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 via-sky-50 to-green-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <span class="text-4xl">🗺️</span>
                <span class="text-3xl font-bold text-blue-600">AliButuan</span>
            </a>
            <p class="text-gray-500 text-sm mt-1">Butuan City Event Discovery</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @yield('content')
        </div>

        {{-- Back to home --}}
        <p class="text-center mt-6 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-blue-600 transition-colors">← Back to Home</a>
        </p>
    </div>

</body>
</html>
