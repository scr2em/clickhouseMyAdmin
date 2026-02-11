@extends('layouts.app')

@section('title', $database . ' — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">{{ $database }}</span>
@endsection

@section('content')
<div class="max-w-5xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $database }}</h1>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Table</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Engine</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Rows</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Size</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tables as $tbl)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">
                        <a href="{{ route('table.data', [$database, $tbl['name']]) }}" class="text-blue-600 hover:underline font-medium">{{ $tbl['name'] }}</a>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ $tbl['engine'] }}</td>
                    <td class="px-4 py-2 text-right text-sm text-gray-600">{{ $tbl['total_rows'] !== null ? number_format($tbl['total_rows']) : '—' }}</td>
                    <td class="px-4 py-2 text-right text-sm text-gray-600">{{ $tbl['size'] ?? '—' }}</td>
                    <td class="px-4 py-2 text-right space-x-2">
                        <a href="{{ route('table.structure', [$database, $tbl['name']]) }}" class="text-sm text-gray-500 hover:text-blue-600">Structure</a>
                        <a href="{{ route('table.data', [$database, $tbl['name']]) }}" class="text-sm text-gray-500 hover:text-blue-600">Data</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">No tables in this database</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
