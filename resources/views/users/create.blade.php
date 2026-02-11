@extends('layouts.app')

@section('title', 'Create User — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <a href="{{ route('users.index') }}" class="hover:text-blue-600">Users</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">Create</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Create User</h1>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @csrf

        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" required
                   pattern="[a-zA-Z0-9_]+"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   placeholder="my_user">
            <p class="text-xs text-gray-500 mt-1">Letters, numbers, and underscores only.</p>
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label for="default_database" class="block text-sm font-medium text-gray-700 mb-1">Default Database</label>
            <select name="default_database" id="default_database"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">— none —</option>
                @foreach($allDatabases as $db)
                    <option value="{{ $db }}" {{ old('default_database') === $db ? 'selected' : '' }}>{{ $db }}</option>
                @endforeach
            </select>
        </div>

        @if(!empty($roles))
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Default Roles</label>
            <div class="space-y-1 max-h-40 overflow-y-auto border border-gray-200 rounded p-3">
                @foreach($roles as $role)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="roles[]" value="{{ $role }}"
                           {{ in_array($role, old('roles', [])) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    {{ $role }}
                </label>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                Create User
            </button>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
