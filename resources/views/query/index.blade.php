@extends('layouts.app')

@section('title', 'SQL Query — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">SQL Query</span>
@endsection

@section('content')
<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">SQL Query</h1>

    <div class="mb-4 px-3 py-2 bg-amber-50 border border-amber-200 rounded text-sm text-amber-800">
        ClickHouse does not support multiple statements in a single query.
    </div>

    <form method="POST" action="{{ route('query.execute') }}" id="query-form"
          hx-post="{{ route('query.execute') }}"
          hx-target="#query-results"
          hx-select="#query-results"
          hx-swap="outerHTML"
          hx-boost="false">
        @csrf
        <div class="mb-3 flex items-center gap-3">
            <label class="text-sm text-gray-600">Database:</label>
            <select name="database" id="query-database-select" class="border border-gray-300 rounded px-3 py-1.5 text-sm bg-white">
                <option value="">— default —</option>
                @foreach($databases as $db)
                    <option value="{{ $db }}" {{ $selectedDatabase === $db ? 'selected' : '' }}>{{ $db }}</option>
                @endforeach
            </select>
            <div class="ml-auto flex gap-2">
                <button type="button" onclick="openAiModal(document.getElementById('query-database-select').value, null)"
                    class="px-3 py-1.5 bg-purple-600 text-white text-sm font-medium rounded hover:bg-purple-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    AI Assist
                </button>
                <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                    Execute (Ctrl+Enter)
                </button>
            </div>
        </div>
        <div class="mb-4">
            <textarea name="sql" id="sql-editor"
                class="w-full h-48 font-mono text-sm border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                placeholder="SELECT 1">{{ $sql }}</textarea>
        </div>
    </form>

    @include('query.partials.results')
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.createSqlEditor) {
        window.createSqlEditor('sql-editor', {
            placeholder: 'SELECT 1',
            schema: @json($schema ?? []),
            minHeight: '180px',
        });
    }
});
</script>
@endsection
