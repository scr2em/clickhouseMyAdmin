@php
    $isActive = isset($database) && $database === $db;
    $currentTable = isset($table) ? $table : null;
@endphp

<details class="mb-1" {{ $isActive ? 'open' : '' }}
    hx-get="{{ route('database.show', $db) }}"
    hx-trigger="toggle once"
    hx-target="#sidebar-tables-{{ Str::slug($db) }}"
    hx-swap="innerHTML">
    <summary class="flex items-center gap-1.5 w-full px-3 py-1.5 rounded text-sm hover:bg-gray-800 cursor-pointer list-none {{ $isActive ? 'bg-gray-800 text-white' : '' }}">
        <svg class="w-3 h-3 transition-transform details-arrow" fill="currentColor" viewBox="0 0 20 20"><path d="M6 4l8 6-8 6V4z"/></svg>
        <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
        <span class="truncate">{{ $db }}</span>
    </summary>
    <div class="ml-5 mt-0.5" id="sidebar-tables-{{ Str::slug($db) }}" hx-boost="true" hx-target="main" hx-select="main" hx-swap="outerHTML">
        @if($isActive && isset($tables))
            @include('components.sidebar-tables', ['db' => $db, 'tables' => collect($tables)->pluck('name'), 'currentTable' => $currentTable])
        @endif
    </div>
</details>
