<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ClickHouse Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" hx-boost="true" hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'>
    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside id="sidebar" class="flex flex-col bg-gray-900 text-gray-300 transition-all duration-200 w-64">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                <a href="{{ route('dashboard') }}" class="text-white font-bold text-lg">CH Admin</a>
            </div>
            <nav class="flex-1 overflow-y-auto px-2 py-3">
                <a href="{{ route('query.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm hover:bg-gray-800 mb-2 {{ request()->routeIs('query.*') ? 'bg-gray-800 text-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    SQL Query
                </a>
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm hover:bg-gray-800 mb-2 {{ request()->routeIs('users.*') ? 'bg-gray-800 text-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Users
                </a>
                <a href="{{ route('settings.index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded text-sm hover:bg-gray-800 mb-2 {{ request()->routeIs('settings.*') ? 'bg-gray-800 text-white' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
                <div class="text-xs uppercase tracking-wider text-gray-500 px-3 mb-2">Databases</div>
                @if(isset($databases))
                    @foreach($databases as $db)
                        @include('components.sidebar', ['db' => $db])
                    @endforeach
                @endif
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200 px-4 py-2 flex items-center gap-3">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <nav class="text-sm text-gray-500">
                    @yield('breadcrumb')
                </nav>
            </header>
            <main class="flex-1 overflow-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
    {{-- AI Assist Modal --}}
    <div id="ai-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40" onclick="closeAiModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg flex flex-col">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h3 class="font-medium text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    AI Assist
                </h3>
                <button onclick="closeAiModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Describe what you want to query</label>
                <textarea id="ai-prompt" rows="3"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-y"
                    placeholder="e.g. Show me the top 10 rows ordered by date"></textarea>
                <div id="ai-error" class="hidden mt-2 text-sm text-red-600"></div>
                <div id="ai-result" class="hidden mt-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Generated SQL</label>
                    <pre id="ai-sql" class="bg-gray-50 border border-gray-200 rounded p-3 text-sm font-mono whitespace-pre-wrap break-words max-h-48 overflow-auto"></pre>
                </div>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2">
                <button id="ai-copy-btn" onclick="copyAiSql()" class="hidden px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded">Copy</button>
                <button id="ai-use-btn" onclick="useAiSql()" class="hidden px-3 py-1.5 text-sm bg-green-600 text-white rounded hover:bg-green-700">Use in Editor</button>
                <button id="ai-generate-btn" onclick="generateAiSql()" class="px-3 py-1.5 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    Generate
                </button>
                <button onclick="closeAiModal()" class="px-3 py-1.5 text-sm bg-gray-200 hover:bg-gray-300 rounded">Close</button>
            </div>
        </div>
    </div>

    <script>
    let aiModalDatabase = null;
    let aiModalTable = null;
    let aiGeneratedSql = '';

    function openAiModal(database, table) {
        aiModalDatabase = database || null;
        aiModalTable = table || null;
        aiGeneratedSql = '';

        document.getElementById('ai-prompt').value = '';
        document.getElementById('ai-error').classList.add('hidden');
        document.getElementById('ai-result').classList.add('hidden');
        document.getElementById('ai-copy-btn').classList.add('hidden');
        document.getElementById('ai-use-btn').classList.add('hidden');
        document.getElementById('ai-generate-btn').disabled = false;
        document.getElementById('ai-generate-btn').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg> Generate';

        document.getElementById('ai-modal').classList.remove('hidden');
        document.getElementById('ai-prompt').focus();
    }

    function closeAiModal() {
        document.getElementById('ai-modal').classList.add('hidden');
    }

    function generateAiSql() {
        const prompt = document.getElementById('ai-prompt').value.trim();
        if (!prompt) return;

        const btn = document.getElementById('ai-generate-btn');
        const errorEl = document.getElementById('ai-error');
        const resultEl = document.getElementById('ai-result');

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Generating...';
        errorEl.classList.add('hidden');
        resultEl.classList.add('hidden');
        document.getElementById('ai-copy-btn').classList.add('hidden');
        document.getElementById('ai-use-btn').classList.add('hidden');

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/ai/generate-sql', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                prompt: prompt,
                database: aiModalDatabase,
                table: aiModalTable,
            }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                aiGeneratedSql = data.sql;
                document.getElementById('ai-sql').textContent = data.sql;
                resultEl.classList.remove('hidden');
                document.getElementById('ai-copy-btn').classList.remove('hidden');
                document.getElementById('ai-use-btn').classList.remove('hidden');
            } else {
                errorEl.textContent = data.error || 'Failed to generate SQL.';
                errorEl.classList.remove('hidden');
            }
        })
        .catch(err => {
            errorEl.textContent = 'Request failed: ' + err.message;
            errorEl.classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg> Generate';
        });
    }

    function copyAiSql() {
        navigator.clipboard.writeText(aiGeneratedSql);
        const btn = document.getElementById('ai-copy-btn');
        btn.textContent = 'Copied!';
        setTimeout(() => { btn.textContent = 'Copy'; }, 1500);
    }

    function useAiSql() {
        // Find the active CodeMirror editor on the page
        const editors = document.querySelectorAll('.cm-editor');
        if (editors.length > 0) {
            const editorEl = editors[editors.length - 1];
            const view = editorEl.cmView?.view;
            if (view) {
                view.dispatch({
                    changes: { from: 0, to: view.state.doc.length, insert: aiGeneratedSql }
                });
            }
        }

        // Also update hidden textarea as fallback
        const textareas = ['sql-editor', 'table-sql-editor'];
        for (const id of textareas) {
            const el = document.getElementById(id);
            if (el) {
                el.value = aiGeneratedSql;
                el.dispatchEvent(new Event('input'));
            }
        }

        closeAiModal();
    }

    // Allow Enter in prompt to generate (Ctrl+Enter or Cmd+Enter)
    document.addEventListener('keydown', function(e) {
        if (e.target.id === 'ai-prompt' && e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            generateAiSql();
        }
    });
    </script>
</body>
</html>
