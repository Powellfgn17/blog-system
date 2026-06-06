@extends('layouts.app')

@section('content')
@php
    $isEdit = isset($post) && $post->exists;
    $formAction = $isEdit ? route('blog.update', $post) : route('blog.store');
    $existingTags = $isEdit ? $post->tags->pluck('name')->implode(', ') : '';
    $currentStatus = old('status', $isEdit ? $post->status : 'draft');
@endphp

<div class="min-h-screen bg-slate-50 -mt-10">

    {{-- ─── Editor Top Bar ──────────────────────────────────────── --}}
    <div class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-[1400px] mx-auto px-6 md:px-10 h-14 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('blog.index') }}" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-800 transition-colors group">
                    <span translate="no" class="material-symbols-outlined text-[18px] transform group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                    <span class="font-ui-small text-sm hidden sm:inline">Retour au Blog</span>
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
                    <span translate="no" class="material-symbols-outlined text-[16px]">save</span>
                    Sauvegarder
                </button>
                {{-- Preview --}}
                <button type="button" onclick="openPreview()"
                    class="hidden sm:flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                    <span translate="no" class="material-symbols-outlined text-[16px]">open_in_new</span>
                    Aperçu
                </button>
                {{-- Publish --}}
                <button type="button" onclick="submitAs('published')"
                    class="flex items-center gap-2 px-5 py-2 text-sm font-bold text-white bg-slate-900 hover:bg-slate-700 rounded-lg transition-colors shadow-sm">
                    <span translate="no" class="material-symbols-outlined text-[16px]">publish</span>
                    {{ $isEdit ? 'Mettre à jour' : 'Publier sur le Blog' }}
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
             LEFT — Main Article Content
        ══════════════════════════════════════════════════════ --}}
        <div class="flex flex-col gap-6 min-w-0">

            {{-- Title --}}
            <div>
                <input
                    type="text"
                    name="title"
                    id="article-title"
                    value="{{ old('title', $isEdit ? $post->title : '') }}"
                    placeholder="Titre de l'article editorial…"
                    autocomplete="off"
                    class="w-full bg-transparent border-0 border-b-2 border-slate-200 focus:border-slate-800 focus:ring-0 text-slate-900 placeholder-slate-300 font-display text-4xl md:text-5xl font-extrabold tracking-tight py-4 px-0 leading-tight transition-colors"
                    style="font-family: 'Hanken Grotesk', sans-serif;"
                    required
                >
                @error('title') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- Cover Image Dropzone --}}
            <div class="relative group">
                <div id="cover-drop-zone"
                    class="relative w-full aspect-[21/8] rounded-2xl border-2 border-dashed border-slate-200 bg-white hover:border-slate-400 overflow-hidden transition-all duration-200 cursor-pointer flex items-center justify-center"
                    onclick="document.getElementById('cover_image').click()">

                    {{-- Placeholder --}}
                    <div id="cover-placeholder" class="flex flex-col items-center gap-3 text-slate-400 pointer-events-none {{ ($isEdit && $post->getOriginal('cover_image_url')) ? 'hidden' : '' }}">
                        <span translate="no" class="material-symbols-outlined text-[48px]" style="font-variation-settings: 'FILL' 0, 'wght' 300;">add_photo_alternate</span>
                        <div class="text-center">
                            <p class="text-sm font-semibold text-slate-500">Cliquer pour ajouter une image de couverture</p>
                            <p class="text-xs text-slate-400 mt-0.5">Format recommandé : 21:8 (2100×800px) · JPG, PNG, WebP</p>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <img
                        id="cover-preview"
                        src="{{ ($isEdit && $post->getOriginal('cover_image_url')) ? asset($post->getOriginal('cover_image_url')) : '#' }}"
                        alt="Aperçu couverture"
                        class="absolute inset-0 w-full h-full object-cover {{ ($isEdit && $post->getOriginal('cover_image_url')) ? '' : 'hidden' }}"
                    >

                    {{-- Hover Overlay --}}
                    <div id="cover-overlay" class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity {{ ($isEdit && $post->getOriginal('cover_image_url')) ? '' : 'hidden' }}">
                        <div class="flex items-center gap-2 bg-white/90 text-slate-800 text-sm font-semibold px-4 py-2 rounded-full shadow">
                            <span translate="no" class="material-symbols-outlined text-[18px]">edit</span>
                            Changer l'image
                        </div>
                    </div>
                </div>

                <input type="file" id="cover_image" name="cover_image" class="hidden" accept="image/*" onchange="previewCover(this)">

                @if($isEdit && $post->getOriginal('cover_image_url'))
                    <button type="button" onclick="removeCover()" class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full flex items-center justify-center shadow hover:bg-white transition-colors">
                        <span translate="no" class="material-symbols-outlined text-[16px] text-slate-600">close</span>
                    </button>
                @endif

                @error('cover_image') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- Rich Text Editor --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">

                {{-- Toolbar --}}
                <div class="border-b border-slate-100 bg-slate-50 px-4 py-2 flex items-center gap-1 flex-wrap">
                    @foreach([
                        ['**','**','format_bold','Gras'],
                        ['*','*','format_italic','Italique'],
                        ['~~','~~','format_strikethrough','Barré'],
                    ] as [$pre,$suf,$icon,$label])
                    <button type="button" onclick="insertMarkdown('{{ $pre }}','{{ $suf }}')" title="{{ $label }}"
                        class="p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition-colors">
                        <span translate="no" class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                    </button>
                    @endforeach

                    <div class="w-px h-5 bg-slate-200 mx-1"></div>

                    @foreach([
                        ['# ','','title','H1'],
                        ['## ','','h_mobiledata','H2'],
                        ['### ','','h_plus_mobiledata','H3'],
                    ] as [$pre,$suf,$icon,$label])
                    <button type="button" onclick="insertMarkdown('{{ $pre }}','{{ $suf }}')" title="{{ $label }}"
                        class="p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition-colors text-xs font-bold w-8 h-8 flex items-center justify-center">
                        {{ $label }}
                    </button>
                    @endforeach

                    <div class="w-px h-5 bg-slate-200 mx-1"></div>

                    @foreach([
                        ['> ','','format_quote','Citation'],
                        ['- ','','format_list_bulleted','Liste'],
                        ['1. ','','format_list_numbered','Numérotée'],
                        ['```\n','\n```','code','Code'],
                        ['[','](url)','link','Lien'],
                        ['![alt](','url)','image','Image'],
                    ] as [$pre,$suf,$icon,$label])
                    <button type="button" onclick="insertMarkdown('{{ $pre }}','{{ $suf }}')" title="{{ $label }}"
                        class="p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition-colors">
                        <span translate="no" class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                    </button>
                    @endforeach

                    <div class="ml-auto flex items-center gap-2 text-xs text-slate-400">
                        <span translate="no" class="material-symbols-outlined text-[14px]">schedule</span>
                        <span id="reading-time-label">1 min de lecture</span>
                        <span class="ml-2" id="word-count">0 mots</span>
                    </div>
                </div>

                {{-- Textarea --}}
                <textarea
                    id="post-body"
                    name="body"
                    rows="28"
                    placeholder="Commencez à rédiger votre article editorial en Markdown…&#10;&#10;Utilisez ## pour les sous-titres, **gras**, *italique*, > citation…"
                    class="w-full bg-white border-0 focus:ring-0 text-slate-800 text-[17px] leading-8 p-8 resize-y font-serif placeholder-slate-300"
                    style="font-family: 'Literata', Georgia, serif;"
                    required
                >{{ old('body', $isEdit ? $post->body : '') }}</textarea>
                @error('body') <p class="px-8 pb-4 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             RIGHT — Editorial Settings Sidebar
        ══════════════════════════════════════════════════════ --}}
        <aside class="flex flex-col gap-4 xl:sticky xl:top-[88px]">

            {{-- ── Publication Status ── --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span translate="no" class="material-symbols-outlined text-[14px]">cloud_upload</span>
                    Statut de publication
                </h3>

                <div class="flex flex-col gap-2">
                    @foreach(['draft' => ['Brouillon','save','slate'], 'published' => ['Publié','check_circle','emerald'], 'scheduled' => ['Planifié','schedule','amber']] as $val => [$lbl, $icon, $color])
                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                        {{ $currentStatus === $val ? 'border-slate-800 bg-slate-50' : 'border-slate-100 hover:border-slate-300' }}
                        status-option" data-value="{{ $val }}">
                        <input type="radio" name="_status_radio" value="{{ $val }}" class="sr-only"
                            {{ $currentStatus === $val ? 'checked' : '' }}
                            onchange="selectStatus('{{ $val }}')">
                        <span translate="no" class="material-symbols-outlined text-[18px] text-{{ $color }}-600">{{ $icon }}</span>
                        <span class="text-sm font-semibold text-slate-700">{{ $lbl }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Scheduled Date/Time --}}
                <div id="scheduled-dt" class="{{ $currentStatus === 'scheduled' ? '' : 'hidden' }} mt-3">
                    <label class="block text-xs text-slate-500 mb-1 font-medium">Date et heure de publication</label>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at', $isEdit && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                        class="w-full text-sm border border-slate-200 rounded-lg px-3 py-2 focus:border-slate-700 focus:ring-1 focus:ring-slate-700 text-slate-700">
                </div>
            </div>

            {{-- ── Category ── --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span translate="no" class="material-symbols-outlined text-[14px]">category</span>
                    Catégorie
                </h3>
                <div class="relative">
                    <select name="category_id"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 py-3 pl-4 pr-10 appearance-none focus:border-slate-700 focus:ring-1 focus:ring-slate-700 cursor-pointer">
                        <option value="">— Sans catégorie —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $isEdit ? $post->category_id : '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <span translate="no" class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-[18px]">expand_more</span>
                </div>
                @error('category_id') <p class="mt-1 text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            {{-- ── Tags ── --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-2">
                    <span translate="no" class="material-symbols-outlined text-[14px]">sell</span>
                    Tags
                </h3>
                <p class="text-xs text-slate-400 mb-3">Séparez les tags par des virgules.</p>
                <div class="relative">
                    <input type="text" name="tags" id="tags-input"
                        value="{{ old('tags', $existingTags) }}"
                        placeholder="Design, Architecture, Tech…"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 py-3 px-4 focus:border-slate-700 focus:ring-1 focus:ring-slate-700 placeholder-slate-300">
                </div>
                <div id="tags-preview" class="flex flex-wrap gap-1.5 mt-3"></div>
            </div>

            {{-- ── Featured Toggle ── --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                            <span translate="no" class="material-symbols-outlined text-[16px] text-amber-500">star</span>
                            Article Featured
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Affiché en héro sur la page d'accueil du Blog.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer ml-4">
                        <input type="checkbox" name="is_featured" value="1" id="is-featured"
                            class="sr-only peer"
                            {{ old('is_featured', $isEdit ? $post->is_featured : false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-slate-800 peer-focus:ring-2 peer-focus:ring-slate-300 transition-colors after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-5"></div>
                    </label>
                </div>
            </div>

            {{-- ── SEO Metadata ── --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span translate="no" class="material-symbols-outlined text-[14px]">travel_explore</span>
                    SEO & Métadonnées
                </h3>

                <div class="flex flex-col gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">Slug URL</label>
                        <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden focus-within:border-slate-700 focus-within:ring-1 focus-within:ring-slate-700 transition-all">
                            <span class="text-xs text-slate-400 px-3 py-3 bg-slate-50 border-r border-slate-200 whitespace-nowrap select-none">/blog/</span>
                            <input type="text" name="slug" id="slug-input"
                                value="{{ old('slug', $isEdit ? $post->slug : '') }}"
                                placeholder="mon-article-editorial"
                                class="flex-1 bg-white border-0 focus:ring-0 text-sm text-slate-700 py-3 px-3 placeholder-slate-300">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">
                            Meta description
                            <span id="meta-char-count" class="ml-1 text-slate-400 font-normal">(0/160)</span>
                        </label>
                        <textarea name="meta_description" id="meta-description" rows="3"
                            maxlength="160"
                            placeholder="Résumé de l'article pour les moteurs de recherche (max 160 caractères)…"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 py-3 px-4 focus:border-slate-700 focus:ring-1 focus:ring-slate-700 resize-none placeholder-slate-300"
                        >{{ old('meta_description', $isEdit ? $post->meta_description : '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── Editorial Review Checklist ── --}}
            <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span translate="no" class="material-symbols-outlined text-[14px] text-slate-400">fact_check</span>
                    Revue Éditoriale
                </h3>
                <div class="flex flex-col gap-3">
                    @foreach([
                        ['grammar','Vérification grammaticale effectuée'],
                        ['alt_text','Texte alternatif sur toutes les images'],
                        ['links','Liens vérifiés et fonctionnels'],
                        ['sources','Sources citées et crédibles'],
                        ['seo','Slug et méta-description complétés'],
                    ] as [$id,$label])
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" id="check-{{ $id }}"
                            class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-slate-100 focus:ring-slate-400 checked:bg-white checked:border-white">
                        <span class="text-sm text-slate-300 group-hover:text-white transition-colors">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-slate-700">
                    <div class="flex justify-between text-xs text-slate-400 mb-2">
                        <span>Progression</span>
                        <span id="checklist-count">0/5</span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                        <div id="checklist-bar" class="h-full bg-emerald-400 rounded-full transition-all duration-300" style="width:0%"></div>
                    </div>
                </div>
            </div>

            {{-- Mobile action buttons --}}
            <div class="xl:hidden flex flex-col gap-2">
                <button type="button" onclick="submitAs('draft')"
                    class="w-full py-3 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                    <span translate="no" class="material-symbols-outlined text-[16px]">save</span>
                    Sauvegarder le brouillon
                </button>
                <button type="button" onclick="submitAs('published')"
                    class="w-full py-3 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-700 transition-colors flex items-center justify-center gap-2">
                    <span translate="no" class="material-symbols-outlined text-[16px]">publish</span>
                    {{ $isEdit ? 'Mettre à jour' : 'Publier sur le Blog' }}
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

function removeCover() {
    const preview = document.getElementById('cover-preview');
    const placeholder = document.getElementById('cover-placeholder');
    const overlay = document.getElementById('cover-overlay');
    preview.src = '#';
    preview.classList.add('hidden');
    placeholder.classList.remove('hidden');
    if (overlay) overlay.classList.add('hidden');
    document.getElementById('cover_image').value = '';
}

// ─── Status selector ─────────────────────────────────────────
function selectStatus(val) {
    document.getElementById('form-status').value = val;
    const scheduledDt = document.getElementById('scheduled-dt');
    scheduledDt.classList.toggle('hidden', val !== 'scheduled');

    document.querySelectorAll('.status-option').forEach(el => {
        const isSelected = el.dataset.value === val;
        el.classList.toggle('border-slate-800', isSelected);
        el.classList.toggle('bg-slate-50', isSelected);
        el.classList.toggle('border-slate-100', !isSelected);
    });

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

// ─── Auto-slug from title ─────────────────────────────────────
document.getElementById('article-title').addEventListener('input', function() {
    const slugInput = document.getElementById('slug-input');
    if (!slugInput.dataset.manual) {
        slugInput.value = this.value
            .toLowerCase()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-');
    }
});

document.getElementById('slug-input').addEventListener('input', function() {
    this.dataset.manual = 'true';
});

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

// ─── Reading time & word count ────────────────────────────────
function updateStats() {
    const text  = document.getElementById('post-body').value;
    const words = text.trim() ? text.trim().split(/\s+/).length : 0;
    const mins  = Math.max(1, Math.ceil(words / 200));
    document.getElementById('reading-time-label').textContent = mins + ' min de lecture';
    document.getElementById('word-count').textContent = words.toLocaleString('fr-FR') + ' mots';
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
        span.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200';
        span.textContent = '#' + t;
        preview.appendChild(span);
    });
}

document.getElementById('tags-input').addEventListener('input', renderTags);
renderTags();

// ─── Meta description character counter ──────────────────────
document.getElementById('meta-description').addEventListener('input', function() {
    document.getElementById('meta-char-count').textContent = '(' + this.value.length + '/160)';
});

// Trigger on load if editing
(function() {
    const meta = document.getElementById('meta-description');
    if (meta.value.length) {
        document.getElementById('meta-char-count').textContent = '(' + meta.value.length + '/160)';
    }
})();

// ─── Editorial checklist progress ────────────────────────────
function updateChecklist() {
    const boxes = document.querySelectorAll('[id^="check-"]');
    const checked = [...boxes].filter(b => b.checked).length;
    document.getElementById('checklist-count').textContent = checked + '/' + boxes.length;
    document.getElementById('checklist-bar').style.width = (checked / boxes.length * 100) + '%';
}

document.querySelectorAll('[id^="check-"]').forEach(b => b.addEventListener('change', updateChecklist));

// ─── Preview button ───────────────────────────────────────────
function openPreview() {
    @if($isEdit)
        window.open('{{ route("blog.show", $post) }}', '_blank');
    @else
        alert('Enregistrez d\'abord l\'article pour pouvoir le prévisualiser.');
    @endif
}
</script>
@endsection
