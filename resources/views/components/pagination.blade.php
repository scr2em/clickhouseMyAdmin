@if($totalPages > 1)
<nav class="flex items-center justify-between mt-4">
    <div class="text-sm text-gray-500">
        Showing {{ number_format(($page - 1) * $perPage + 1) }} to {{ number_format(min($page * $perPage, $totalRows)) }} of {{ number_format($totalRows) }} rows
    </div>
    <div class="flex items-center gap-1">
        @if($page > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}"
               hx-get="{{ request()->fullUrlWithQuery(['page' => $page - 1]) }}"
               hx-target="#data-results"
               hx-select="#data-results"
               hx-swap="outerHTML"
               hx-boost="false"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">Previous</a>
        @endif

        @php
            $start = max(1, $page - 2);
            $end = min($totalPages, $page + 2);
        @endphp

        @if($start > 1)
            <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
               hx-get="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
               hx-target="#data-results"
               hx-select="#data-results"
               hx-swap="outerHTML"
               hx-boost="false"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">1</a>
            @if($start > 2)
                <span class="px-1 text-gray-400">...</span>
            @endif
        @endif

        @for($i = $start; $i <= $end; $i++)
            <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
               hx-get="{{ request()->fullUrlWithQuery(['page' => $i]) }}"
               hx-target="#data-results"
               hx-select="#data-results"
               hx-swap="outerHTML"
               hx-boost="false"
               class="px-3 py-1 text-sm border rounded {{ $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white border-gray-300 hover:bg-gray-50' }}">{{ $i }}</a>
        @endfor

        @if($end < $totalPages)
            @if($end < $totalPages - 1)
                <span class="px-1 text-gray-400">...</span>
            @endif
            <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}"
               hx-get="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}"
               hx-target="#data-results"
               hx-select="#data-results"
               hx-swap="outerHTML"
               hx-boost="false"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">{{ $totalPages }}</a>
        @endif

        @if($page < $totalPages)
            <a href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}"
               hx-get="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}"
               hx-target="#data-results"
               hx-select="#data-results"
               hx-swap="outerHTML"
               hx-boost="false"
               class="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50">Next</a>
        @endif
    </div>
</nav>
@endif
