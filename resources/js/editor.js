import { Editor, Node, mergeAttributes } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import TextAlign from '@tiptap/extension-text-align';
import TextStyle from '@tiptap/extension-text-style';
import Color from '@tiptap/extension-color';
import Highlight from '@tiptap/extension-highlight';
import Placeholder from '@tiptap/extension-placeholder';
import CharacterCount from '@tiptap/extension-character-count';

// কবিতার লাইনের জন্য আলাদা ব্লক
const Poem = Node.create({
    name: 'poem',
    group: 'block',
    content: 'inline*',
    parseHTML: () => [{ tag: 'p.poem' }],
    renderHTML: ({ HTMLAttributes }) => ['p', mergeAttributes(HTMLAttributes, { class: 'poem' }), 0],
});

// <mark> HTMLPurifier চেনে না, তাই span.hl ব্যবহার
const Marker = Highlight.extend({
    parseHTML: () => [{ tag: 'span.hl' }, { tag: 'mark' }],
    renderHTML: () => ['span', { class: 'hl' }, 0],
});

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;
const bn = (n) => new Intl.NumberFormat('bn-BD').format(n);

function initEditor(root) {
    const input = root.querySelector('input[type=hidden]');
    const surface = root.querySelector('[data-surface]');
    const fileInput = root.querySelector('[data-file]');
    const blockSel = root.querySelector('[data-block]');
    const colorInput = root.querySelector('[data-color]');
    const countEl = root.querySelector('[data-count]');

    async function upload(file) {
        const fd = new FormData();
        fd.append('image', file);
        const res = await fetch(root.dataset.upload, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf(), Accept: 'application/json' },
            body: fd,
        });
        if (!res.ok) throw new Error('upload failed');
        return (await res.json()).url;
    }

    async function insertFile(file) {
        if (!file || !file.type.startsWith('image/')) return;
        try {
            const url = await upload(file);
            editor.chain().focus().setImage({ src: url, alt: file.name.replace(/\.[^.]+$/, '') }).run();
        } catch (e) {
            alert('ছবি আপলোড হয়নি (সর্বোচ্চ ৩ MB, jpg/png/webp/gif)।');
        }
    }

    const editor = new Editor({
        element: surface,
        content: input.value || '',
        extensions: [
            StarterKit.configure({ heading: { levels: [2, 3, 4] } }),
            Underline,
            Link.configure({ openOnClick: false }),
            Image,
            TextAlign.configure({ types: ['heading', 'paragraph', 'poem'] }),
            TextStyle,
            Color,
            Marker,
            Poem,
            CharacterCount,
            Placeholder.configure({ placeholder: root.dataset.placeholder || 'এখানে লিখুন...' }),
        ],
        editorProps: {
            attributes: { class: 'article-body editor-surface' },
            handlePaste: (_v, e) => {
                const f = [...(e.clipboardData?.files || [])].find((f) => f.type.startsWith('image/'));
                if (!f) return false;
                e.preventDefault();
                insertFile(f);
                return true;
            },
            handleDrop: (_v, e) => {
                const f = [...(e.dataTransfer?.files || [])].find((f) => f.type.startsWith('image/'));
                if (!f) return false;
                e.preventDefault();
                insertFile(f);
                return true;
            },
        },
        onUpdate: ({ editor }) => {
            input.value = editor.isEmpty ? '' : editor.getHTML();
        },
        onTransaction: () => refresh(),
    });

    const chain = () => editor.chain().focus();

    const run = {
        undo: () => chain().undo().run(),
        redo: () => chain().redo().run(),
        bold: () => chain().toggleBold().run(),
        italic: () => chain().toggleItalic().run(),
        underline: () => chain().toggleUnderline().run(),
        strike: () => chain().toggleStrike().run(),
        highlight: () => chain().toggleHighlight().run(),
        'align-left': () => chain().setTextAlign('left').run(),
        'align-center': () => chain().setTextAlign('center').run(),
        'align-right': () => chain().setTextAlign('right').run(),
        'align-justify': () => chain().setTextAlign('justify').run(),
        bullet: () => chain().toggleBulletList().run(),
        ordered: () => chain().toggleOrderedList().run(),
        quote: () => chain().toggleBlockquote().run(),
        hr: () => chain().setHorizontalRule().run(),
        unlink: () => chain().unsetLink().run(),
        clear: () => chain().unsetAllMarks().clearNodes().run(),
        image: () => fileInput.click(),
        full: () => root.classList.toggle('is-full'),
        link: () => {
            const prev = editor.getAttributes('link').href || 'https://';
            const url = prompt('লিংকের ঠিকানা (খালি রাখলে লিংক মুছবে)', prev);
            if (url === null) return;
            if (url === '') return chain().unsetLink().run();
            chain().extendMarkRange('link').setLink({ href: url }).run();
        },
    };

    const active = {
        bold: () => editor.isActive('bold'),
        italic: () => editor.isActive('italic'),
        underline: () => editor.isActive('underline'),
        strike: () => editor.isActive('strike'),
        highlight: () => editor.isActive('highlight'),
        bullet: () => editor.isActive('bulletList'),
        ordered: () => editor.isActive('orderedList'),
        quote: () => editor.isActive('blockquote'),
        link: () => editor.isActive('link'),
        'align-left': () => editor.isActive({ textAlign: 'left' }),
        'align-center': () => editor.isActive({ textAlign: 'center' }),
        'align-right': () => editor.isActive({ textAlign: 'right' }),
        'align-justify': () => editor.isActive({ textAlign: 'justify' }),
    };

    root.querySelectorAll('[data-cmd]').forEach((btn) => {
        btn.addEventListener('mousedown', (e) => e.preventDefault()); // selection হারাবে না
        btn.addEventListener('click', () => run[btn.dataset.cmd]?.());
    });

    blockSel.addEventListener('change', () => {
        const v = blockSel.value;
        if (v === 'paragraph') chain().setParagraph().run();
        else if (v === 'poem') chain().setNode('poem').run();
        else chain().setHeading({ level: Number(v.slice(1)) }).run();
    });

    colorInput.addEventListener('input', () => chain().setColor(colorInput.value).run());
    root.querySelector('[data-color-reset]')?.addEventListener('click', () => chain().unsetColor().run());

    fileInput.addEventListener('change', () => {
        insertFile(fileInput.files[0]);
        fileInput.value = '';
    });

    function refresh() {
        root.querySelectorAll('[data-cmd]').forEach((btn) => {
            btn.classList.toggle('is-active', !!active[btn.dataset.cmd]?.());
        });
        const h = [2, 3, 4].find((l) => editor.isActive('heading', { level: l }));
        blockSel.value = h ? 'h' + h : editor.isActive('poem') ? 'poem' : 'paragraph';
        countEl.textContent = bn(editor.storage.characterCount.words()) + ' শব্দ';
    }
    refresh();
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-editor]').forEach(initEditor);
});