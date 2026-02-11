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

    <form method="POST" action="{{ route('query.execute') }}" id="query-form"
          hx-post="{{ route('query.execute') }}"
          hx-target="#query-results"
          hx-select="#query-results"
          hx-swap="outerHTML"
          hx-boost="false">
        @csrf
        <div class="mb-3 flex items-center gap-3">
            <label class="text-sm text-gray-600">Database:</label>
            <select name="database" class="border border-gray-300 rounded px-3 py-1.5 text-sm bg-white">
                <option value="">— default —</option>
                @foreach($databases as $db)
                    <option value="{{ $db }}" {{ $selectedDatabase === $db ? 'selected' : '' }}>{{ $db }}</option>
                @endforeach
            </select>
            <button type="submit" class="ml-auto px-4 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                Execute (Ctrl+Enter)
            </button>
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
