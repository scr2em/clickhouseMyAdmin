@extends('layouts.app')

@section('title', $table . ' Structure — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <a href="{{ route('database.show', $database) }}" class="hover:text-blue-600">{{ $database }}</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">{{ $table }}</span>
    <span class="mx-1">/</span>
    <span class="text-gray-700">Structure</span>
@endsection

@section('content')
<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $table }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('table.structure', [$database, $table]) }}"
               class="px-3 py-1.5 text-sm font-medium rounded bg-blue-600 text-white">Structure</a>
            <a href="{{ route('table.data', [$database, $table]) }}"
               class="px-3 py-1.5 text-sm font-medium rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Data</a>
        </div>
    </div>

    {{-- Table Info --}}
    @if(!empty($tableInfo))
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Engine</div>
            <div class="text-sm font-semibold text-gray-900">{{ $tableInfo['engine'] ?? 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Rows</div>
            <div class="text-sm font-semibold text-gray-900">{{ isset($tableInfo['total_rows']) ? number_format($tableInfo['total_rows']) : 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Size</div>
            <div class="text-sm font-semibold text-gray-900">{{ $tableInfo['size'] ?? 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Sorting Key</div>
            <div class="text-sm font-semibold text-gray-900 truncate">{{ $tableInfo['sorting_key'] ?: 'None' }}</div>
        </div>
    </div>
    @endif

    {{-- Columns --}}
    <h2 class="text-lg font-bold text-gray-900 mb-3">Columns</h2>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Default</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">PK</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Sort</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Partition</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($columns as $col)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 font-mono text-sm text-gray-900">{{ $col['name'] }}</td>
                    <td class="px-4 py-2 font-mono text-sm text-gray-600">{{ $col['type'] }}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">
                        @if($col['default_kind'])
                            <span class="text-orange-600">{{ $col['default_kind'] }}</span>
                            {{ $col['default_expression'] }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($col['is_in_primary_key'])
                            <span class="inline-block w-2 h-2 rounded-full bg-yellow-500" title="Primary Key"></span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($col['is_in_sorting_key'])
                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500" title="Sorting Key"></span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-center">
                        @if($col['is_in_partition_key'])
                            <span class="inline-block w-2 h-2 rounded-full bg-green-500" title="Partition Key"></span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-500">{{ $col['comment'] ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CREATE TABLE statement --}}
    @if(!empty($tableInfo['create_table_query']))
    <h2 class="text-lg font-bold text-gray-900 mb-3">CREATE TABLE</h2>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 overflow-x-auto">
        <pre class="text-sm font-mono text-gray-800 whitespace-pre-wrap">{{ $tableInfo['create_table_query'] }}</pre>
    </div>
    @endif
</div>
@endsection
