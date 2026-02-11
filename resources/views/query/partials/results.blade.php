<div id="query-results">
    @if($result !== null)
        @if(!$result['success'])
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="text-sm font-medium text-red-800 mb-1">Error</div>
                <pre class="text-sm text-red-700 whitespace-pre-wrap">{{ $result['error'] }}</pre>
            </div>
        @elseif(!empty($result['meta']))
            <div class="text-sm text-gray-500 mb-2">
                {{ $result['rows'] ?? 0 }} rows returned
                @if(!empty($result['statistics']))
                    — {{ round($result['statistics']['elapsed'] ?? 0, 3) }}s elapsed,
                    {{ number_format($result['statistics']['rows_read'] ?? 0) }} rows read,
                    {{ number_format($result['statistics']['bytes_read'] ?? 0) }} bytes read
                @endif
            </div>
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
