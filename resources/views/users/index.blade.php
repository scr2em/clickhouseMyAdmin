@extends('layouts.app')

@section('title', 'Users — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">Users</span>
@endsection

@section('content')
<div class="max-w-5xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Users</h1>
        <a href="{{ route('users.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
            Create User
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Auth Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Default Database</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">
                        <a href="{{ route('users.show', $u['name']) }}" class="text-blue-600 hover:underline font-medium">{{ $u['name'] }}</a>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ $u['auth_type'] ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm text-gray-600">{{ $u['default_database'] ?: '—' }}</td>
                    <td class="px-4 py-2 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('users.show', $u['name']) }}" class="text-sm text-gray-500 hover:text-blue-600">View</a>
                            <form method="POST" action="{{ route('users.destroy', $u['name']) }}"
                                  onsubmit="return confirm('Drop user \'{{ $u['name'] }}\'? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-500 hover:text-red-700">Drop</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-400">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
