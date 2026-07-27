(function (window, document, $) {
    'use strict';

    const SELECTORS = '.code-editor-tiny, .code-editor';

        const text = String(html || '')
            .replace(/<[^>]+>/g, ' ')
            .replace(/&nbsp;/gi, ' ')
            .replace(/\s+/g, ' ')
            .trim();
        if (!text) return 0;
        return text.split(' ').length;
    }

    function toolbarHtml() {
        return `
            <div class="visa-editor__toolbar" role="toolbar">
                <div class="ve-group">
                    <button type="button" data-cmd="undo" title="Undo"><i class="fa fa-undo"></i></button>
                    <button type="button" data-cmd="redo" title="Redo"><i class="fa fa-repeat"></i></button>
                </div>
                <div class="ve-group">
                    <select data-block title="Blocks">
                        <option value="P">Paragraph</option>
                        <option value="H1">Heading 1</option>
                        <option value="H2">Heading 2</option>
                        <option value="H3">Heading 3</option>
                        <option value="H4">Heading 4</option>
                        <option value="BLOCKQUOTE">Quote</option>
                        <option value="PRE">Preformatted</option>
                    </select>
                </div>
                <div class="ve-group">
                    <button type="button" data-cmd="bold" title="Bold"><i class="fa fa-bold"></i></button>
                    <button type="button" data-cmd="italic" title="Italic"><i class="fa fa-italic"></i></button>
                    <button type="button" data-cmd="underline" title="Underline"><i class="fa fa-underline"></i></button>
                    <button type="button" data-cmd="strikeThrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></button>
                </div>
                <div class="ve-group">
                    <button type="button" data-cmd="justifyLeft" title="Align left"><i class="fa fa-align-left"></i></button>
                    <button type="button" data-cmd="justifyCenter" title="Align center"><i class="fa fa-align-center"></i></button>
                    <button type="button" data-cmd="justifyRight" title="Align right"><i class="fa fa-align-right"></i></button>
                    <button type="button" data-cmd="justifyFull" title="Justify"><i class="fa fa-align-justify"></i></button>
                </div>
                <div class="ve-group">
                    <button type="button" data-cmd="insertUnorderedList" title="Bullet list"><i class="fa fa-list-ul"></i></button>
                    <button type="button" data-cmd="insertOrderedList" title="Numbered list"><i class="fa fa-list-ol"></i></button>
                    <button type="button" data-cmd="outdent" title="Decrease indent"><i class="fa fa-outdent"></i></button>
                    <button type="button" data-cmd="indent" title="Increase indent"><i class="fa fa-indent"></i></button>
                </div>
                <div class="ve-group">
                    <button type="button" data-action="link" title="Insert link"><i class="fa fa-link"></i></button>
                    <button type="button" data-action="unlink" title="Remove link"><i class="fa fa-unlink"></i></button>
                    <button type="button" data-action="image" title="Insert image"><i class="fa fa-image"></i></button>
                    <button type="button" data-action="hr" title="Horizontal line"><i class="fa fa-minus"></i></button>
                </div>
                <div class="ve-group">
                    <input type="color" data-action="foreColor" title="Text color" value="#222222">
                    <input type="color" data-action="hiliteColor" title="Highlight" value="#ffff00">
                    <button type="button" data-cmd="removeFormat" title="Clear formatting"><i class="fa fa-eraser"></i></button>
                    <button type="button" data-action="code" title="Source code"><i class="fa fa-code"></i></button>
                </div>
            </div>
        `;
    }

    function createShell(textarea, options) {
        const height = options && options.height ? Number(options.height) : 320;
        const wrap = document.createElement('div');
        wrap.className = 'visa-editor tox tox-tinymce';
        wrap.innerHTML = `
            ${toolbarHtml()}
            <div class="visa-editor__surface" contenteditable="true"></div>
            <textarea class="visa-editor__code" spellcheck="false"></textarea>
            <div class="visa-editor__status"><span class="ve-words">0 words</span></div>
        `;

        const surface = wrap.querySelector('.visa-editor__surface');
        const code = wrap.querySelector('.visa-editor__code');
        const words = wrap.querySelector('.ve-words');
        surface.style.minHeight = `${height}px`;
        code.style.minHeight = `${height}px`;
        surface.innerHTML = textarea.value || '';
        code.value = textarea.value || '';
        words.textContent = `${countWords(surface.innerHTML)} words`;

        textarea.style.display = 'none';
        textarea.setAttribute('data-visa-editor', '1');
        textarea.parentNode.insertBefore(wrap, textarea.nextSibling);

        return { wrap, surface, code, words, textarea, height };
    }

    function syncToTextarea(editor) {
        const html = editor.wrap.classList.contains('is-code')
            ? editor.code.value
            : editor.surface.innerHTML;
        editor.textarea.value = html;
        editor.words.textContent = `${countWords(html)} words`;
    }

    function exec(cmd, value) {
        document.execCommand(cmd, false, value ?? null);
    }

    function mount(textarea, options) {
        if (!textarea || textarea.dataset.visaEditorMounted === '1') {
            return textarea && textarea._visaEditor ? textarea._visaEditor : null;
        }

        const editor = createShell(textarea, options || {});
        textarea.dataset.visaEditorMounted = '1';
        textarea._visaEditor = editor;

        const run = (command) => {
            if (editor.wrap.classList.contains('is-code')) return;
            editor.surface.focus();
            exec(command);
            syncToTextarea(editor);
        };

        editor.wrap.querySelectorAll('[data-cmd]').forEach((btn) => {
            btn.addEventListener('mousedown', (e) => e.preventDefault());
            btn.addEventListener('click', () => run(btn.getAttribute('data-cmd')));
        });

        const blockSelect = editor.wrap.querySelector('[data-block]');
        blockSelect.addEventListener('change', () => {
            if (editor.wrap.classList.contains('is-code')) return;
            editor.surface.focus();
            exec('formatBlock', blockSelect.value);
            syncToTextarea(editor);
        });

        editor.wrap.querySelectorAll('[data-action]').forEach((el) => {
            el.addEventListener('mousedown', (e) => {
                if (el.tagName !== 'INPUT') e.preventDefault();
            });

            el.addEventListener('click', () => {
                const action = el.getAttribute('data-action');

                if (action === 'code') {
                    const isCode = editor.wrap.classList.toggle('is-code');
                    if (isCode) {
                        editor.code.value = editor.surface.innerHTML;
                        editor.code.focus();
                    } else {
                        editor.surface.innerHTML = editor.code.value;
                        editor.surface.focus();
                    }
                    syncToTextarea(editor);
                    return;
                }

                if (editor.wrap.classList.contains('is-code')) return;
                editor.surface.focus();

                if (action === 'link') {
                    const url = window.prompt('Enter link URL', 'https://');
                    if (url) exec('createLink', url);
                } else if (action === 'unlink') {
                    exec('unlink');
                } else if (action === 'image') {
                    const url = window.prompt('Enter image URL', '');
                    if (url) exec('insertImage', url);
                } else if (action === 'hr') {
                    exec('insertHorizontalRule');
                }

                syncToTextarea(editor);
            });

            if (el.type === 'color') {
                el.addEventListener('input', () => {
                    if (editor.wrap.classList.contains('is-code')) return;
                    editor.surface.focus();
                    const action = el.getAttribute('data-action');
                    if (action === 'foreColor') exec('foreColor', el.value);
                    if (action === 'hiliteColor') {
                        try {
                            exec('hiliteColor', el.value);
                        } catch (e) {
                            exec('backColor', el.value);
                        }
                    }
                    syncToTextarea(editor);
                });
            }
        });

        editor.surface.addEventListener('input', () => syncToTextarea(editor));
        editor.code.addEventListener('input', () => syncToTextarea(editor));
        editor.surface.addEventListener('blur', () => syncToTextarea(editor));
        editor.code.addEventListener('blur', () => syncToTextarea(editor));

        const form = textarea.closest('form');
        if (form && !form.dataset.visaEditorBound) {
            form.dataset.visaEditorBound = '1';
            form.addEventListener('submit', () => {
                form.querySelectorAll('textarea[data-visa-editor="1"]').forEach((area) => {
                    if (area._visaEditor) syncToTextarea(area._visaEditor);
                });
            });
        }

        syncToTextarea(editor);
        return editor;
    }

    function destroyNearbyClones(root) {
        const scope = root || document;
        scope.querySelectorAll('.visa-editor, .tox.tox-tinymce').forEach((node) => {
            const prev = node.previousElementSibling;
            if (prev && prev.matches && prev.matches(SELECTORS)) {
                prev.dataset.visaEditorMounted = '';
                prev._visaEditor = null;
                prev.style.display = '';
            }
            node.remove();
        });
    }

    function init(selector, options) {
        const nodes = typeof selector === 'string'
            ? document.querySelectorAll(selector)
            : (selector && selector.length !== undefined ? selector : [selector]);

        Array.prototype.forEach.call(nodes || [], (node) => {
            if (!node) return;
            if (node.tagName === 'TEXTAREA') {
                mount(node, options);
            }
        });
    }

    function initAll(options) {
        init(SELECTORS, options || { height: 500 });
    }

    const VisaEditor = {
        mount,
        init,
        initAll,
        destroyNearbyClones,
        selectors: SELECTORS,
    };

    window.VisaEditor = VisaEditor;

    // TinyMCE-compatible shims so existing dashboard scripts keep working.
    window.tinymce = {
        init(config) {
            const selector = (config && config.selector) || SELECTORS;
            init(selector, config || {});
        },
        remove(target) {
            if (!target) {
                destroyNearbyClones(document);
                return;
            }
            const el = typeof target === 'string' ? document.querySelector(target) : target;
            if (el && el._visaEditor) {
                el._visaEditor.wrap.remove();
                el.dataset.visaEditorMounted = '';
                el._visaEditor = null;
                el.style.display = '';
            }
        },
    };

    if ($ && $.fn) {
        $.fn.tinymce = function (config) {
            return this.each(function () {
                mount(this, config || {});
            });
        };
    }

    window.createEditor = function (selector) {
        init(selector || SELECTORS, { height: 500 });
    };

    window.tinymceInitEditor = async function (ele) {
        if (ele && ele.each) {
            ele.each(function () {
                mount(this, { height: 500 });
            });
            return;
        }
        if (ele && ele[0]) {
            mount(ele[0], { height: 500 });
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        initAll({ height: 500 });
    });
})(window, document, window.jQuery);
