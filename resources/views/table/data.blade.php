@extends('layouts.app')

@section('title', $table . ' — ClickHouse Admin')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
    <span class="mx-1">/</span>
    <a href="{{ route('database.show', $database) }}" class="hover:text-blue-600">{{ $database }}</a>
    <span class="mx-1">/</span>
    <span class="text-gray-700 font-medium">{{ $table }}</span>
@endsection

@section('content')
<div>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold text-gray-900">{{ $table }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('table.data', [$database, $table]) }}"
               class="px-3 py-1.5 text-sm font-medium rounded bg-blue-600 text-white">Data</a>
            <a href="{{ route('table.structure', [$database, $table]) }}"
               class="px-3 py-1.5 text-sm font-medium rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Structure</a>
        </div>
    </div>

    {{-- Query editor --}}
    <form method="POST" action="{{ route('table.data', [$database, $table]) }}" id="table-query-form" class="mb-4"
          hx-post="{{ route('table.data', [$database, $table]) }}"
          hx-target="#data-results"
          hx-select="#data-results"
          hx-swap="outerHTML"
          hx-boost="false">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3">
            <textarea name="sql" id="table-sql-editor"
                class="w-full h-24 font-mono text-sm border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 resize-y"
                placeholder="SELECT * FROM {{ $database }}.{{ $table }} LIMIT 50">{{ $sql }}</textarea>
            <div class="flex items-center justify-between mt-2">
                <span class="text-xs text-gray-400">Ctrl+Enter to run</span>
                <div class="flex gap-2">
                    @if($isCustomQuery)
                        <a href="{{ route('table.data', [$database, $table]) }}"
                           class="px-3 py-1.5 text-sm font-medium rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">Reset</a>
                    @endif
                    <button type="button" onclick="openAiModal('{{ $database }}', '{{ $table }}')"
                        class="px-3 py-1.5 bg-purple-600 text-white text-sm font-medium rounded hover:bg-purple-700 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        AI Assist
                    </button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                        Run Query
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Results --}}
    @include('table.partials.data-results')

    {{-- JSON viewer/editor modal --}}
    <div id="json-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40" onclick="closeJsonModal()"></div>
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[80vh] flex flex-col">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
                <h3 class="font-medium text-gray-900" id="json-modal-title"></h3>
                <button onclick="closeJsonModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-auto p-4">
                <textarea id="json-modal-editor"
                    class="w-full font-mono text-sm border border-gray-300 rounded p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y bg-gray-50" style="min-height: 500px"></textarea>
                <div id="json-modal-feedback" class="mt-2 text-sm hidden"></div>
            </div>
            <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2">
                <button onclick="copyJsonValue()" id="json-modal-copy" class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded">Copy</button>
                <button id="json-modal-save" class="hidden px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700" onclick="saveJsonEdit()">Save</button>
                <button onclick="closeJsonModal()" class="px-3 py-1.5 text-sm bg-gray-200 hover:bg-gray-300 rounded">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.createSqlEditor) {
        window.createSqlEditor('table-sql-editor', {
            placeholder: 'SELECT * FROM {{ $database }}.{{ $table }} LIMIT 50',
            schema: @json($schema ?? []),
        });
    }
});

const UPDATE_URL = '{{ route("table.updateCell", [$database, $table]) }}';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

function postCellUpdate(column, value, rowJson) {
    return fetch(UPDATE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ column, value, wheres: JSON.parse(rowJson) }),
    }).then(r => r.text().then(text => ({ ok: r.ok, text })));
}

// --- Inline cell editing (non-JSON) ---

function startInlineEdit(div, column, currentValue, rowJson) {
    if (div.querySelector('input')) return;

    const originalText = div.textContent.trim();
    div.classList.remove('truncate');
    div.innerHTML = '';

    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentValue;
    input.className = 'w-full px-1 py-0.5 text-sm font-mono border border-blue-400 rounded bg-white focus:outline-none focus:ring-1 focus:ring-blue-500';
    div.appendChild(input);
    input.focus();
    input.select();

    let saving = false;

    function cancel() {
        if (saving) return;
        div.innerHTML = '';
        div.textContent = originalText;
        div.classList.add('truncate');
    }

    function save() {
        const newValue = input.value;
        if (newValue === currentValue) { cancel(); return; }

        saving = true;
        input.disabled = true;
        input.classList.add('opacity-50');

        postCellUpdate(column, newValue, rowJson)
            .then(({ ok, text }) => {
                if (ok) {
                    div.innerHTML = '';
                    div.textContent = newValue;
                    div.classList.add('truncate');
                    div.classList.add('bg-green-50');
                    setTimeout(() => div.classList.remove('bg-green-50'), 1500);
                } else {
                    saving = false;
                    input.disabled = false;
                    input.classList.remove('opacity-50');
                    input.classList.add('border-red-500');
                    input.title = text.replace(/<[^>]*>/g, '') || 'Update failed';
                }
            })
            .catch(() => {
                saving = false;
                input.disabled = false;
                input.classList.remove('opacity-50');
                input.classList.add('border-red-500');
            });
    }

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); save(); }
        if (e.key === 'Escape') { e.preventDefault(); cancel(); }
    });

    input.addEventListener('blur', function() {
        setTimeout(cancel, 150);
    });
}

// --- JSON modal ---

let jsonModalSourceCell = null;

function openJsonModal(sourceDiv, column, value, rowJson) {
    const modal = document.getElementById('json-modal');
    const editor = document.getElementById('json-modal-editor');
    const saveBtn = document.getElementById('json-modal-save');
    const feedback = document.getElementById('json-modal-feedback');

    jsonModalSourceCell = sourceDiv;
    document.getElementById('json-modal-title').textContent = column;
    feedback.classList.add('hidden');

    let formatted = value;
    try { formatted = JSON.stringify(JSON.parse(value), null, 2); } catch {}
    editor.value = formatted;
    modal.dataset.rawValue = value;

    if (rowJson) {
        modal.dataset.column = column;
        modal.dataset.row = rowJson;
        editor.readOnly = false;
        editor.classList.remove('bg-gray-100');
        editor.classList.add('bg-gray-50');
        saveBtn.classList.remove('hidden');
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save';
    } else {
        editor.readOnly = true;
        editor.classList.remove('bg-gray-50');
        editor.classList.add('bg-gray-100');
        saveBtn.classList.add('hidden');
    }

    modal.classList.remove('hidden');
    document.getElementById('json-modal-copy').textContent = 'Copy';
}

function closeJsonModal() {
    document.getElementById('json-modal').classList.add('hidden');
    jsonModalSourceCell = null;
}

function copyJsonValue() {
    const editor = document.getElementById('json-modal-editor');
    const btn = document.getElementById('json-modal-copy');
    navigator.clipboard.writeText(editor.value);
    btn.textContent = 'Copied!';
    setTimeout(() => { btn.textContent = 'Copy'; }, 1500);
}

function saveJsonEdit() {
    const modal = document.getElementById('json-modal');
    const editor = document.getElementById('json-modal-editor');
    const saveBtn = document.getElementById('json-modal-save');
    const feedback = document.getElementById('json-modal-feedback');

    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';
    feedback.classList.add('hidden');

    let value = editor.value;
    try { value = JSON.stringify(JSON.parse(value)); } catch {}

    postCellUpdate(modal.dataset.column, value, modal.dataset.row)
        .then(({ ok, text }) => {
            feedback.classList.remove('hidden', 'text-red-600', 'text-green-600');
            if (ok) {
                feedback.classList.add('text-green-600');
                feedback.textContent = 'Updated successfully.';
                if (jsonModalSourceCell) {
                    jsonModalSourceCell.textContent = value;
                    jsonModalSourceCell.classList.add('bg-green-50');
                    setTimeout(() => jsonModalSourceCell.classList.remove('bg-green-50'), 1500);
                }
                setTimeout(() => closeJsonModal(), 1000);
            } else {
                feedback.classList.add('text-red-600');
                feedback.textContent = text.replace(/<[^>]*>/g, '') || 'Update failed.';
            }
        })
        .catch(err => {
            feedback.classList.remove('hidden', 'text-red-600', 'text-green-600');
            feedback.classList.add('text-red-600');
            feedback.textContent = 'Request failed: ' + err.message;
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save';
        });
}
</script>
@endsection
