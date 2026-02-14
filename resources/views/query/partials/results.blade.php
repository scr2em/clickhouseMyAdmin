<div id="query-results">
    @if($result !== null)
        @if(!$result['success'])
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="text-sm font-medium text-red-800 mb-1">Error</div>
                <pre class="text-sm text-red-700 whitespace-pre-wrap">{{ $result['error'] }}</pre>
            </div>
        @elseif(!empty($result['meta']))
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm text-gray-500">
                    {{ $result['rows'] ?? 0 }} rows returned
                    @if(!empty($result['statistics']))
                        — {{ round($result['statistics']['elapsed'] ?? 0, 3) }}s elapsed,
                        {{ number_format($result['statistics']['rows_read'] ?? 0) }} rows read,
                        {{ number_format($result['statistics']['bytes_read'] ?? 0) }} bytes read
                    @endif
                </div>
                <div class="flex bg-gray-100 rounded-lg p-0.5">
                    <button onclick="switchQueryView('table', this)"
                            class="query-view-btn px-3 py-1 text-xs font-medium rounded-md bg-white text-gray-700 shadow-sm">
                        Table
                    </button>
                    <button onclick="switchQueryView('json', this)"
                            class="query-view-btn px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">
                        JSON
                    </button>
                </div>
            </div>

            {{-- Table View --}}
            <div id="query-table-view">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach($result['meta'] as $col)
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                                        {{ $col['name'] }}
                                        <span class="text-gray-400 normal-case">({{ $col['type'] }})</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($result['data'] as $row)
                            <tr class="hover:bg-gray-50">
                                @foreach($result['meta'] as $col)
                                    <td class="px-3 py-2 text-sm font-mono text-gray-700 max-w-md truncate whitespace-nowrap">{{ is_array($v = $row[$col['name']] ?? null) ? json_encode($v) : ($v ?? 'NULL') }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- JSON View --}}
            <div id="query-json-view" class="hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 relative">
                    <button onclick="copyJsonToClipboard('query-json-content')"
                            class="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-gray-600 bg-white rounded border border-gray-200 hover:border-gray-300"
                            title="Copy JSON">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                    <pre id="query-json-content" class="p-4 text-sm font-mono text-gray-700 overflow-x-auto whitespace-pre max-h-[600px] overflow-y-auto">{{ json_encode(json_decode($result['rawJson'] ?? '{}'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-800">Query executed successfully.</div>
                @if(!empty($result['raw']))
                    <pre class="text-sm text-green-700 mt-2 whitespace-pre-wrap">{{ $result['raw'] }}</pre>
                @endif
            </div>
        @endif
    @endif
</div>
