@extends('layouts.app')

@section('title', 'Settings — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">Settings</span>
@endsection

@section('content')
<div class="max-w-xl">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Settings</h1>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded bg-green-50 border border-green-200 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded bg-red-50 border border-red-200 text-red-800 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('settings.store') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" hx-boost="false">
        @csrf
        <div class="mb-4">
            <label for="anthropic_api_key" class="block text-sm font-medium text-gray-700 mb-1">Anthropic API Key</label>
            <input type="password" name="anthropic_api_key" id="anthropic_api_key"
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="{{ $hasApiKey ? $maskedKey : 'sk-ant-...' }}"
                required>
            @if($hasApiKey)
                <p class="mt-1 text-xs text-gray-500">Current key: {{ $maskedKey }}</p>
            @endif
            <p class="mt-1 text-xs text-gray-500">
                Get your API key from
                <a href="https://console.anthropic.com/" target="_blank" class="text-blue-600 hover:underline">console.anthropic.com</a>
            </p>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
            Save Settings
        </button>
    </form>
</div>
@endsection
