<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Error — ClickHouse Admin</title>
    @vite(['resources/css/app.css'])
</head>
<body class="h-full flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg border border-gray-200 p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 mb-2">Cannot Connect to ClickHouse</h1>
        <p class="text-gray-600 mb-6">
            Unable to reach the ClickHouse server. Please check your connection settings.
        </p>
        <div class="bg-gray-50 rounded-lg p-4 text-left text-sm font-mono text-gray-700 mb-6">
            <div>CLICKHOUSE_HOST: {{ config('clickhouse.host') }}</div>
            <div>CLICKHOUSE_PORT: {{ config('clickhouse.port') }}</div>
            <div>CLICKHOUSE_USER: {{ config('clickhouse.username') }}</div>
        </div>
        <a href="{{ url('/') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
            Retry Connection
        </a>
    </div>
</body>
</html>
