@extends('layouts.app')

@section('content')
@php
    $isEdit = isset($post) && $post->exists;
    $formAction = $isEdit ? route('community.update', $post) : route('community.store');
    $existingTags = $isEdit ? $post->tags->pluck('name')->implode(', ') : '';
    $currentStatus = old('status', $isEdit ? $post->status : 'draft');
@endphp

<style>
    /* @username suggestions dropdown */
    #mention-suggestions {
        position: absolute;
        z-index: 100;
        min-width: 220px;
        max-height: 240px;
        overflow-y: auto;
    }
    #mention-suggestions li {
        cursor: pointer;
    }
    #mention-suggestions li:hover,
    #mention-suggestions li.active {
        background-color: #EEF2FF;
    }
</style>

<div class="min-h-screen bg-slate-50">

    {{-- ─── Editor Top Bar ──────────────────────────────────────── --}}
    <div class="sticky top-[80px] z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-[1400px] mx-auto px-6 md:px-10 h-14 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('community.index') }}" class="flex items-center gap-1.5 text-slate-500 hover:text-community-indigo transition-colors group">
                    <span class="material-symbols-outlined text-[18px] transform group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                    <span class="font-ui-small text-sm hidden sm:inline">Retour à la Communauté</span>
                </a>
                <span class="text-slate-300 select-none">|</span>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ $currentStatus === 'published' ? 'bg-emerald-500' : ($currentStatus === 'scheduled' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                    <span class="font-ui-small text-xs text-slate-500 capitalize">
                        {{ $currentStatus === 'draft' ? 'Brouillon' : ($currentStatus === 'published' ? 'Publié' : 'Planifié') }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Save Draft --}}
                <button type="button" onclick="submitAs('draft')"
                    class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Sauvegarder
                </button>
                {{-- Publish --}}
                <button type="button" onclick="submitAs('published')"
                    class="flex items-center gap-2 px-5 py-2 text-sm font-bold text-white bg-community-indigo hover:bg-community-indigo/90 rounded-lg transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    {{ $isEdit ? 'Mettre à jour' : 'Publier' }}
                </button>
            </div>
        </div>
    </div>

    {{-- ─── Main 2-Column Layout ──────────────────────────────────── --}}
    <form
        id="article-form"
        method="POST"
        action="{{ $formAction }}"
        enctype="multipart/form-data"
        class="max-w-[1400px] mx-auto px-4 md:px-10 py-8 grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-8 items-start"
    >
        @csrf
        @if($isEdit) @method('PUT') @endif
        <input type="hidden" name="status" id="form-status" value="{{ $currentStatus }}">

        {{-- ══════════════════════════════════════════════════════
             LEFT — Main Content
        ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col gap-6 min-w-0">

            {{-- Title --}}
            <div>
                <input
                    type="text"
                    name="title"
                    id="article-title"
                    value="{{ old('title', $isEdit ? $post->title : '') }}"
                    placeholder="Donnez un titre accrocheur à votre publication…"
                    autocomplete="off"
                    class="w-full bg-transparent border-0 border-b-2 border-slate-200 focus:border-community-indigo focus:ring-0 text-slate-900 placeholder-slate-300 font-display text-4xl md:text-5xl font-extrabold tracking-tight py-4 px-0 leading-tight transition-colors"
                    style="font-family: 'Hanken Grotesk', sans-serif;"
                    required
                >
                @error('title') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- Cover Image Dropzone (1:1 Ratio for community) --}}
            <div class="relative group mx-auto w-full max-w-md">
                <div id="cover-drop-zone"
                    class="relative w-full aspect-square rounded-2xl border-2 border-dashed border-slate-200 bg-white hover:border-community-indigo/50 overflow-hidden transition-all duration-200 cursor-pointer flex items-center justify-center"
                    onclick="document.getElementById('cover_image').click()">

                    {{-- Placeholder --}}
                    <div id="cover-placeholder" class="flex flex-col items-center gap-3 text-slate-400 pointer-events-none {{ ($isEdit && $post->getOriginal('cover_image_url')) ? 'hidden' : '' }}">
                        <span class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 0, 'wght' 300;">add_photo_alternate</span>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-slate-500">Ajouter une image</p>
                            <p class="text-xs text-slate-400 mt-0.5">Ratio 1:1 recommandé</p>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <img
                        id="cover-preview"
                        src="{{ ($isEdit && $post->getOriginal('cover_image_url')) ? asset($post->getOriginal('cover_image_url')) : '#' }}"
                        alt="Aperçu image"
                        class="absolute inset-0 w-full h-full object-cover {{ ($isEdit && $post->getOriginal('cover_image_url')) ? '' : 'hidden' }}"
                    >

                    {{-- Hover Overlay --}}
                    <div id="cover-overlay" class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity {{ ($isEdit && $post->getOriginal('cover_image_url')) ? '' : 'hidden' }}">
                        <div class="flex items-center gap-2 bg-white/90 text-slate-800 text-sm font-semibold px-4 py-2 rounded-full shadow">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                            Changer l'image
                        </div>
                    </div>
                </div>

                <input type="file" id="cover_image" name="cover_image" class="hidden" accept="image/*" onchange="previewCover(this)">

                @if($isEdit && $post->getOriginal('cover_image_url'))
                    <button type="button" onclick="removeCover(event)" class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center shadow hover:bg-white transition-colors">
                        <span class="material-symbols-outlined text-[16px] text-slate-600">close</span>
                    </button>
                @endif

                @error('cover_image') <p class="mt-1 text-red-500 text-xs text-center">{{ $message }}</p> @enderror
            </div>

            {{-- Rich Text Editor --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm relative">

                {{-- Toolbar --}}
                <div class="border-b border-slate-100 bg-slate-50 px-4 py-2 flex items-center gap-1 flex-wrap">
                    @foreach([
                        ['**','**','format_bold','Gras'],
                        ['*','*','format_italic','Italique'],
                    ] as [$pre,$suf,$icon,$label])
                    <button type="button" onclick="insertMarkdown('{{ $pre }}','{{ $suf }}')" title="{{ $label }}"
                        class="p-2 rounded-lg text-slate-500 hover:text-community-indigo hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                    </button>
                    @endforeach

                    <div class="w-px h-5 bg-slate-200 mx-1"></div>

                    @foreach([
                        ['## ','','h_mobiledata','H2'],
                        ['### ','','h_plus_mobiledata','H3'],
                    ] as [$pre,$suf,$icon,$label])
                    <button type="button" onclick="insertMarkdown('{{ $pre }}','{{ $suf }}')" title="{{ $label }}"
                        class="p-2 rounded-lg text-slate-500 hover:text-community-indigo hover:bg-slate-200 transition-colors text-xs font-bold w-8 h-8 flex items-center justify-center">
                        {{ $label }}
                    </button>
                    @endforeach

                    <div class="w-px h-5 bg-slate-200 mx-1"></div>

                    @foreach([
                        ['> ','','format_quote','Citation'],
                        ['- ','','format_list_bulleted','Liste'],
                    ] as [$pre,$suf,$icon,$label])
                    <button type="button" onclick="insertMarkdown('{{ $pre }}','{{ $suf }}')" title="{{ $label }}"
                        class="p-2 rounded-lg text-slate-500 hover:text-community-indigo hover:bg-slate-200 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                    </button>
                    @endforeach

                    <div class="ml-auto flex items-center gap-2 text-xs text-slate-400">
                        <span class="material-symbols-outlined text-[14px]">schedule</span>
                        <span id="reading-time-label">1 min de lecture</span>
                    </div>
                </div>

                {{-- Textarea --}}
                <textarea
                    id="post-body"
                    name="body"
                    rows="20"
                    placeholder="Partagez vos réflexions... Tapez @ pour mentionner un membre."
                    class="w-full bg-white border-0 focus:ring-0 text-slate-800 text-[18px] leading-8 p-8 resize-y font-serif placeholder-slate-300"
                    style="font-family: 'Literata', Georgia, serif;"
                    required
                >{{ old('body', $isEdit ? $post->body : '') }}</textarea>
                @error('body') <p class="px-8 pb-4 text-red-500 text-xs">{{ $message }}</p> @enderror

                {{-- @mention suggestions dropdown --}}
                <ul id="mention-suggestions" class="hidden bg-white border border-slate-200 rounded-xl shadow-xl py-1">
                </ul>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             RIGHT — Sidebar
        ══════════════════════════════════════════════════════ --}}
        <aside class="flex flex-col gap-4 xl:sticky xl:top-[136px]">

            {{-- ── Settings ── --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px]">tune</span>
                    Paramètres
                </h3>

                {{-- Status --}}
                <div class="flex flex-col gap-2 mb-4">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Visibilité</label>
                    <div class="relative">
                        <select name="status" onchange="selectStatus(this.value)"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 py-3 pl-4 pr-10 appearance-none focus:border-community-indigo focus:ring-1 focus:ring-community-indigo cursor-pointer">
                            <option value="draft" {{ $currentStatus === 'draft' ? 'selected' : '' }}>Brouillon (Privé)</option>
                            <option value="published" {{ $currentStatus === 'published' ? 'selected' : '' }}>Publié (Public)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                    </div>
                </div>

                {{-- Category --}}
                <div class="flex flex-col gap-2 mb-4">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Catégorie <span class="text-slate-400 font-normal">(optionnel)</span></label>
                    <div class="relative">
                        <select name="category_id"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 py-3 pl-4 pr-10 appearance-none focus:border-community-indigo focus:ring-1 focus:ring-community-indigo cursor-pointer">
                            <option value="">— Sans catégorie —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $isEdit ? $post->category_id : '') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                    </div>
                    @error('category_id') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
                </div>

                {{-- Tags --}}
                <div class="flex flex-col gap-2">
                    <label class="block text-xs font-medium text-slate-500 mb-1">Tags <span class="text-slate-400 font-normal">(séparés par des virgules)</span></label>
                    <div class="relative">
                        <input type="text" name="tags" id="tags-input"
                            value="{{ old('tags', $existingTags) }}"
                            placeholder="Design, Architecture…"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 py-3 px-4 focus:border-community-indigo focus:ring-1 focus:ring-community-indigo placeholder-slate-300">
                    </div>
                    <div id="tags-preview" class="flex flex-wrap gap-1.5 mt-2"></div>
                </div>
            </div>

            {{-- ── Community Guidelines ── --}}
            <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5 shadow-sm">
                <h3 class="text-xs font-bold text-community-indigo uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[14px]">local_police</span>
                    Règles communautaires
                </h3>
                <ul class="flex flex-col gap-3">
                    <li class="flex items-start gap-2 text-sm text-slate-700">
                        <span class="material-symbols-outlined text-[16px] mt-0.5 text-community-indigo flex-shrink-0">check_circle</span>
                        Respectez les autres membres et leurs opinions.
                    </li>
                    <li class="flex items-start gap-2 text-sm text-slate-700">
                        <span class="material-symbols-outlined text-[16px] mt-0.5 text-community-indigo flex-shrink-0">check_circle</span>
                        Pas de spam, de harcèlement ni de désinformation.
                    </li>
                    <li class="flex items-start gap-2 text-sm text-slate-700">
                        <span class="material-symbols-outlined text-[16px] mt-0.5 text-community-indigo flex-shrink-0">check_circle</span>
                        Citez vos sources et respectez les droits d'auteur.
                    </li>
                </ul>
            </div>

            {{-- Mobile action buttons --}}
            <div class="xl:hidden flex flex-col gap-2 mt-4">
                <button type="button" onclick="submitAs('draft')"
                    class="w-full py-3 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Sauvegarder le brouillon
                </button>
                <button type="button" onclick="submitAs('published')"
                    class="w-full py-3 text-sm font-bold text-white bg-community-indigo rounded-xl hover:bg-community-indigo/90 transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    {{ $isEdit ? 'Mettre à jour' : 'Publier' }}
                </button>
            </div>

        </aside>
    </form>
</div>

<script>
// ─── Form status submission ──────────────────────────────────
function submitAs(status) {
    document.getElementById('form-status').value = status;
    document.getElementById('article-form').submit();
}

function selectStatus(val) {
    document.getElementById('form-status').value = val;
    // Update top-bar status dot
    const dot = document.querySelector('.w-2.h-2.rounded-full');
    const label = dot ? dot.nextElementSibling : null;
    if (dot) {
        dot.className = 'w-2 h-2 rounded-full ' + (val === 'published' ? 'bg-emerald-500' : val === 'scheduled' ? 'bg-amber-500' : 'bg-slate-400');
    }
    if (label) {
        label.textContent = val === 'published' ? 'Publié' : val === 'scheduled' ? 'Planifié' : 'Brouillon';
    }
}

// ─── Cover image preview ─────────────────────────────────────
function previewCover(input) {
    const preview = document.getElementById('cover-preview');
    const placeholder = document.getElementById('cover-placeholder');
    const overlay = document.getElementById('cover-overlay');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            overlay.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeCover(e) {
    e.stopPropagation();
    const preview = document.getElementById('cover-preview');
    const placeholder = document.getElementById('cover-placeholder');
    const overlay = document.getElementById('cover-overlay');
    preview.src = '#';
    preview.classList.add('hidden');
    placeholder.classList.remove('hidden');
    if (overlay) overlay.classList.add('hidden');
    document.getElementById('cover_image').value = '';
}

// ─── Markdown toolbar ─────────────────────────────────────────
function insertMarkdown(prefix, suffix) {
    const textarea = document.getElementById('post-body');
    const start = textarea.selectionStart;
    const end   = textarea.selectionEnd;
    const text  = textarea.value;
    const sel   = text.substring(start, end);
    textarea.value = text.substring(0, start) + prefix + sel + suffix + text.substring(end);
    textarea.focus();
    if (sel.length === 0) {
        textarea.selectionStart = textarea.selectionEnd = start + prefix.length;
    } else {
        textarea.selectionStart = start;
        textarea.selectionEnd   = start + prefix.length + sel.length + suffix.length;
    }
    updateStats();
}

// ─── Reading time ─────────────────────────────────────────────
function updateStats() {
    const text  = document.getElementById('post-body').value;
    const words = text.trim() ? text.trim().split(/\s+/).length : 0;
    const mins  = Math.max(1, Math.ceil(words / 200));
    document.getElementById('reading-time-label').textContent = mins + ' min de lecture';
}

document.getElementById('post-body').addEventListener('input', updateStats);
updateStats();

// ─── Tags preview ─────────────────────────────────────────────
function renderTags() {
    const val = document.getElementById('tags-input').value;
    const preview = document.getElementById('tags-preview');
    preview.innerHTML = '';
    val.split(',').forEach(t => {
        t = t.trim();
        if (!t) return;
        const span = document.createElement('span');
        span.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 border border-indigo-200';
        span.textContent = '#' + t;
        preview.appendChild(span);
    });
}

document.getElementById('tags-input').addEventListener('input', renderTags);
renderTags();

// ─── @USERNAME MENTIONS ───────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const bodyTA = document.getElementById('post-body');
    const suggestionsList = document.getElementById('mention-suggestions');
    let mentionQuery = null;
    let mentionStart = -1;
    let activeIndex  = -1;
    let fetchTimer   = null;
    let cachedUsers  = [];

    function showSuggestions(users) {
        cachedUsers = users;
        suggestionsList.innerHTML = '';
        if (!users.length) { hideSuggestions(); return; }

        users.forEach((user, i) => {
            const li = document.createElement('li');
            li.className = 'flex items-center gap-3 px-4 py-2 transition-colors';
            li.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    ${user.avatar_url
                        ? `<img src="${user.avatar_url}" class="w-full h-full object-cover" alt="${user.username}">`
                        : `<span class="material-symbols-outlined text-[16px] text-slate-400">person</span>`
                    }
                </div>
                <div class="flex flex-col leading-none">
                    <span class="text-sm font-semibold text-slate-800">${user.name}</span>
                    <span class="text-xs text-slate-500">@${user.username}</span>
                </div>`;
            li.addEventListener('click', () => insertMention(user.username));
            suggestionsList.appendChild(li);
        });

        // Position dropdown
        // (A simple approach: just put it below the textarea for now since we don't have Caret position easily without library)
        const rect = bodyTA.getBoundingClientRect();
        suggestionsList.style.top = (rect.bottom + window.scrollY - 100) + 'px';
        suggestionsList.style.left = (rect.left + 30) + 'px';

        suggestionsList.classList.remove('hidden');
        setActiveItem(0);
    }

    function hideSuggestions() {
        suggestionsList.classList.add('hidden');
        suggestionsList.innerHTML = '';
        mentionQuery = null;
        mentionStart = -1;
        activeIndex  = -1;
        cachedUsers  = [];
    }

    function setActiveItem(idx) {
        const items = suggestionsList.querySelectorAll('li');
        items.forEach((li, i) => li.classList.toggle('active', i === idx));
        activeIndex = idx;
    }

    function insertMention(username) {
        const before = bodyTA.value.substring(0, mentionStart);
        const after  = bodyTA.value.substring(bodyTA.selectionStart);
        bodyTA.value = before + '@' + username + ' ' + after;
        const pos = (before + '@' + username + ' ').length;
        bodyTA.setSelectionRange(pos, pos);
        bodyTA.focus();
        hideSuggestions();
        updateStats();
    }

    bodyTA.addEventListener('input', () => {
        const cursor = bodyTA.selectionStart;
        const text   = bodyTA.value.substring(0, cursor);
        const match  = text.match(/@([\w]*)$/);

        if (match) {
            mentionQuery = match[1];
            mentionStart = cursor - match[0].length;

            clearTimeout(fetchTimer);
            fetchTimer = setTimeout(() => {
                fetch(`/users/search?q=${encodeURIComponent(mentionQuery)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => showSuggestions(data.users ?? data))
                .catch(() => hideSuggestions());
            }, 180);
        } else {
            hideSuggestions();
        }
    });

    bodyTA.addEventListener('keydown', e => {
        if (suggestionsList.classList.contains('hidden')) return;
        const items = suggestionsList.querySelectorAll('li');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActiveItem((activeIndex + 1) % items.length);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActiveItem((activeIndex - 1 + items.length) % items.length);
        } else if (e.key === 'Enter' || e.key === 'Tab') {
            if (activeIndex >= 0 && cachedUsers[activeIndex]) {
                e.preventDefault();
                insertMention(cachedUsers[activeIndex].username);
            }
        } else if (e.key === 'Escape') {
            hideSuggestions();
        }
    });

    document.addEventListener('click', e => {
        if (!suggestionsList.contains(e.target) && e.target !== bodyTA) {
            hideSuggestions();
        }
    });
});
</script>
@endsection
