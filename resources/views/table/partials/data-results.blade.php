<div id="data-results">
    @if(isset($result['error']))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <pre class="text-sm text-red-800 whitespace-pre-wrap">{{ $result['error'] }}</pre>
        </div>
    @elseif(isset($result['success']) && $result['success'] && !empty($result['meta']))
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm text-gray-500">
                @if($isCustomQuery)
                    {{ $result['rows'] ?? 0 }} rows returned
                    @if(!empty($result['statistics']))
                        &mdash; {{ round($result['statistics']['elapsed'] ?? 0, 3) }}s,
                        {{ number_format($result['statistics']['rows_read'] ?? 0) }} rows read
                    @endif
                @else
                    {{ number_format($totalRows) }} rows total &mdash; Page {{ $page }} of {{ number_format($totalPages) }}
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @foreach($result['meta'] as $col)
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                                @if($isCustomQuery)
                                    {{ $col['name'] }}
                                    <span class="text-gray-400 normal-case">({{ $col['type'] }})</span>
                                @else
                                    @php
                                        $isCurrentSort = $orderBy === $col['name'];
                                        $nextDir = ($isCurrentSort && $direction === 'ASC') ? 'DESC' : 'ASC';
                                    @endphp
                                    <a href="{{ request()->fullUrlWithQuery(['order_by' => $col['name'], 'direction' => $nextDir, 'page' => 1]) }}"
                                       class="hover:text-blue-600 inline-flex items-center gap-1"
                                       hx-get="{{ request()->fullUrlWithQuery(['order_by' => $col['name'], 'direction' => $nextDir, 'page' => 1]) }}"
                                       hx-target="#data-results"
                                       hx-select="#data-results"
                                       hx-swap="outerHTML"
                                       hx-boost="false">
                                        {{ $col['name'] }}
                                        @if($isCurrentSort)
                                            <span class="text-blue-600">{{ $direction === 'ASC' ? '↑' : '↓' }}</span>
                                        @endif
                                    </a>
                                @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($result['data'] as $rowIdx => $row)
                    <tr class="hover:bg-gray-50">
                        @foreach($result['meta'] as $col)
                            @php
                                $v = $row[$col['name']] ?? null;
                                $display = is_array($v) ? json_encode($v) : ($v ?? 'NULL');
                                $isLong = is_string($display) && strlen($display) > 60;
                                $isJson = is_array($v) || (is_string($v) && strlen($v) > 1 && ($v[0] === '{' || $v[0] === '['));
                            @endphp
                            <td class="px-3 py-2 text-sm font-mono text-gray-700 max-w-xs whitespace-nowrap group relative">
                                @if($isJson)
                                    <div class="truncate cursor-pointer hover:text-blue-600"
                                         onclick="openJsonModal({{ json_encode($col['name']) }}, {{ json_encode($display) }}{{ !$isCustomQuery ? ', ' . json_encode(json_encode($row)) : '' }})">
                                        {{ $display }}
                                    </div>
                                @elseif(!$isCustomQuery)
                                    <div class="truncate cursor-pointer editable-cell"
                                         onclick="startInlineEdit(this, {{ json_encode($col['name']) }}, {{ json_encode($display) }}, {{ json_encode(json_encode($row)) }})">
                                        {{ $display }}
                                    </div>
                                @else
                                    <div class="truncate">{{ $display }}</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($result['meta']) }}" class="px-4 py-6 text-center text-gray-400">No data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!$isCustomQuery)
            @include('components.pagination')
        @endif
    @elseif(isset($result['success']) && $result['success'])
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-sm text-green-800">Query executed successfully.</div>
            @if(!empty($result['raw']))
                <pre class="text-sm text-green-700 mt-2 whitespace-pre-wrap">{{ $result['raw'] }}</pre>
            @endif
        </div>
    @else
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center text-gray-400">
            No data to display
        </div>
    @endif
</div>
