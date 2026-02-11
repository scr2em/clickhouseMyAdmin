@extends('layouts.app')

@section('title', 'Dashboard — ClickHouse Admin')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Dashboard</span>
@endsection

@section('content')
<div class="max-w-5xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Server Overview</h1>

    @if(!empty($serverInfo))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Version</div>
            <div class="text-lg font-semibold text-gray-900">{{ $serverInfo['version'] ?? 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Uptime</div>
            <div class="text-lg font-semibold text-gray-900">
                @if(isset($serverInfo['uptime']))
                    @php
                        $s = (int)$serverInfo['uptime'];
                        $d = floor($s / 86400);
                        $h = floor(($s % 86400) / 3600);
                        $m = floor(($s % 3600) / 60);
                    @endphp
                    {{ $d }}d {{ $h }}h {{ $m }}m
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Databases</div>
            <div class="text-lg font-semibold text-gray-900">{{ $serverInfo['database_count'] ?? 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Size</div>
            <div class="text-lg font-semibold text-gray-900">{{ $serverInfo['total_size'] ?? 'N/A' }}</div>
        </div>
    </div>
    @endif

    <h2 class="text-lg font-bold text-gray-900 mb-3">Databases</h2>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($databases as $db)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">
                        <a href="{{ route('database.show', $db) }}" class="text-blue-600 hover:underline font-medium">{{ $db }}</a>
                    </td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('database.show', $db) }}" class="text-sm text-gray-500 hover:text-blue-600">Browse tables</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-4 py-6 text-center text-gray-400">No databases found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
