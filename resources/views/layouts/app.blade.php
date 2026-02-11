<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ClickHouse Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" hx-boost="true" hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'>
    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside id="sidebar" class="flex flex-col bg-gray-900 text-gray-300 transition-all duration-200 w-64">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                <a href="{{ route('dashboard') }}" class="text-white font-bold text-lg">CH Admin</a>
            </div>
            <nav class="flex-1 overflow-y-auto px-2 py-3">
                <a href="{{ route('query.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm hover:bg-gray-800 mb-2 {{ request()->routeIs('query.*') ? 'bg-gray-800 text-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    SQL Query
                </a>
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm hover:bg-gray-800 mb-2 {{ request()->routeIs('users.*') ? 'bg-gray-800 text-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Users
                </a>
                <div class="text-xs uppercase tracking-wider text-gray-500 px-3 mb-2">Databases</div>
                @if(isset($databases))
                    @foreach($databases as $db)
                        @include('components.sidebar', ['db' => $db])
                    @endforeach
                @endif
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-2 flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <nav class="text-sm text-gray-500">
                    @yield('breadcrumb')
                </nav>
            </header>
            <main class="flex-1 overflow-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
