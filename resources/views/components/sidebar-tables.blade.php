<a href="{{ route('database.show', $db) }}" class="block px-3 py-1 text-xs text-gray-400 hover:text-white hover:bg-gray-800 rounded">
    View all tables
</a>
@foreach($tables as $t)
    <a href="{{ route('table.data', [$db, $t]) }}"
       class="block px-3 py-1 text-xs hover:bg-gray-800 rounded truncate {{ (isset($currentTable) && $currentTable === $t) ? 'text-white bg-gray-800' : 'text-gray-400 hover:text-white' }}">
        {{ $t }}
    </a>
@endforeach
