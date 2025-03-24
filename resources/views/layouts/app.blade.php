<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Jana') }}</title>

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-indigo-800 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold">Jana</h1>
                <p class="text-sm text-indigo-200">Your Personal Assistant</p>
            </div>
            <nav class="mt-8">
                <div class="px-4 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-900' : 'hover:bg-indigo-700' }}">
                        Dashboard
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                        Projects
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                        Tasks
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                        Diary
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                        Chat
                    </a>
                    <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-indigo-700">
                        Model Comparison
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <div class="bg-white shadow">
                <div class="px-4 py-3 flex justify-between items-center">
                    <h2 class="text-xl font-semibold">@yield('header', 'Dashboard')</h2>
                    <div>
                        <!-- Placeholder for user menu/profile -->
                        <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white">
                            <span>U</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
