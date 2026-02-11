import 'htmx.org';
import { EditorView, keymap, placeholder as cmPlaceholder } from '@codemirror/view';
import { EditorState } from '@codemirror/state';
import { sql, StandardSQL } from '@codemirror/lang-sql';
import { autocompletion } from '@codemirror/autocomplete';
import { basicSetup } from 'codemirror';

window.createSqlEditor = function(elementId, options = {}) {
    const el = document.getElementById(elementId);
    if (!el) return null;

    const textarea = el;
    const wrapper = document.createElement('div');
    wrapper.className = 'cm-wrapper border border-gray-300 rounded overflow-hidden';
    textarea.parentNode.insertBefore(wrapper, textarea);
    textarea.style.display = 'none';

    // Build schema for autocomplete from data attributes
    const schema = {};
    if (options.schema) {
        for (const [table, columns] of Object.entries(options.schema)) {
            schema[table] = columns;
        }
    }

    const submitForm = () => {
        const form = textarea.closest('form');
        if (form) {
            textarea.value = view.state.doc.toString();
            form.requestSubmit();
        }
    };

    const customKeymap = keymap.of([
        {
            key: 'Ctrl-Enter',
            mac: 'Cmd-Enter',
            run: () => { submitForm(); return true; },
        },
    ]);

    const theme = EditorView.theme({
        '&': { fontSize: '13px', backgroundColor: '#f9fafb' },
        '.cm-content': { fontFamily: 'ui-monospace, SFMono-Regular, Menlo, monospace', padding: '8px 0' },
        '.cm-gutters': { backgroundColor: '#f3f4f6', borderRight: '1px solid #e5e7eb', color: '#9ca3af' },
        '.cm-activeLine': { backgroundColor: '#f3f4f6' },
        '.cm-focused .cm-cursor': { borderLeftColor: '#3b82f6' },
        '.cm-focused': { outline: '2px solid #3b82f6', outlineOffset: '-1px' },
        '.cm-tooltip-autocomplete': { border: '1px solid #e5e7eb', borderRadius: '6px', boxShadow: '0 4px 6px -1px rgba(0,0,0,.1)' },
    });

    const view = new EditorView({
        state: EditorState.create({
            doc: textarea.value || '',
            extensions: [
                basicSetup,
                sql({ dialect: StandardSQL, schema, upperCaseKeywords: true }),
                autocompletion({ activateOnTyping: true }),
                customKeymap,
                theme,
                cmPlaceholder(options.placeholder || 'SELECT * FROM ...'),
                EditorView.updateListener.of((update) => {
                    if (update.docChanged) {
                        textarea.value = update.state.doc.toString();
                    }
                }),
            ],
        }),
        parent: wrapper,
    });

    // Set a reasonable height
    const minHeight = options.minHeight || '120px';
    wrapper.style.minHeight = minHeight;

    return view;
};

// Sidebar toggle
window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('w-64');
    sidebar.classList.toggle('w-0');
    sidebar.classList.toggle('overflow-hidden');
};

// Close modals on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const jsonModal = document.getElementById('json-modal');
        if (jsonModal && !jsonModal.classList.contains('hidden')) {
            closeJsonModal();
        }
    }
});
