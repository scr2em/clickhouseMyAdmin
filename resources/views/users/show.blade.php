@extends('layouts.app')

@section('title', $user . ' — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <a href="{{ route('users.index') }}" class="hover:text-blue-600">Users</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">{{ $user }}</span>
@endsection

@section('content')
<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $user }}</h1>

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

    {{-- User Info --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Auth Type</div>
            <div class="text-sm font-semibold text-gray-900">{{ $userInfo['auth_type'] ?? 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Default Database</div>
            <div class="text-sm font-semibold text-gray-900">{{ $userInfo['default_database'] ?: '—' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Host Restrictions</div>
            <div class="text-sm font-semibold text-gray-900 truncate">
                @php
                    $hosts = is_array($userInfo['host_ip'] ?? null) ? $userInfo['host_ip'] : [];
                    $hostNames = is_array($userInfo['host_names'] ?? null) ? $userInfo['host_names'] : [];
                    $allHosts = array_merge($hosts, $hostNames);
                @endphp
                {{ !empty($allHosts) ? implode(', ', $allHosts) : 'Any' }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Default Roles</div>
            <div class="text-sm font-semibold text-gray-900 truncate">
                @if(!empty($userInfo['default_roles_all']))
                    All
                @elseif(!empty($userInfo['default_roles_list']))
                    {{ is_array($userInfo['default_roles_list']) ? implode(', ', $userInfo['default_roles_list']) : $userInfo['default_roles_list'] }}
                @else
                    None
                @endif
            </div>
        </div>
    </div>

    {{-- Grants --}}
    <h2 class="text-lg font-bold text-gray-900 mb-3">Grants</h2>
    <div id="grants-list" class="mb-6">
        @include('users.partials.grants', ['user' => $user, 'grants' => $grants])
    </div>

    {{-- Add Grant --}}
    <h2 class="text-lg font-bold text-gray-900 mb-3">Add Grant</h2>
    <form method="POST" action="{{ route('users.grant', $user) }}"
          hx-post="{{ route('users.grant', $user) }}"
          hx-target="#grants-list"
          hx-swap="innerHTML"
          hx-boost="false"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        @csrf
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Privilege</label>
                <select name="privilege" required class="border border-gray-300 rounded px-3 py-1.5 text-sm bg-white">
                    <option value="SELECT">SELECT</option>
                    <option value="INSERT">INSERT</option>
                    <option value="ALTER">ALTER</option>
                    <option value="CREATE">CREATE</option>
                    <option value="DROP">DROP</option>
                    <option value="TRUNCATE">TRUNCATE</option>
                    <option value="SHOW">SHOW</option>
                    <option value="ALL">ALL</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Database</label>
                <select name="database" class="border border-gray-300 rounded px-3 py-1.5 text-sm bg-white">
                    <option value="*">* (all)</option>
                    @foreach($allDatabases as $db)
                        <option value="{{ $db }}">{{ $db }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Table</label>
                <input type="text" name="table" value="*" placeholder="*"
                       class="border border-gray-300 rounded px-3 py-1.5 text-sm w-32">
            </div>
            <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                Grant
            </button>
        </div>
    </form>

    {{-- Danger Zone --}}
    <h2 class="text-lg font-bold text-red-600 mb-3">Danger Zone</h2>
    <div class="bg-white rounded-lg shadow-sm border border-red-200 p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-900">Drop this user</div>
                <div class="text-sm text-gray-500">This will permanently remove the user and all their grants.</div>
            </div>
            <form method="POST" action="{{ route('users.destroy', $user) }}"
                  onsubmit="return confirm('Drop user \'{{ $user }}\'? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-1.5 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                    Drop User
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
