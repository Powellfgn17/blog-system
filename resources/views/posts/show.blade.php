@extends('layouts.app')

@section('content')
<!-- Main Content Canvas -->
<main class="flex-grow w-full max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12">
    <!-- Back Button -->
    <div class="max-w-article-width mx-auto mb-8 flex justify-start">
        <a href="{{ $context === 'blog' ? route('blog.index') : route('community.index') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-community-indigo dark:text-surface-dim dark:hover:text-secondary-fixed transition-colors font-ui-medium text-ui-medium font-semibold group">
            <span translate="no" class="material-symbols-outlined text-[20px] transform group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Retour
        </a>
    </div>

    <!-- Article Header -->
    <header class="max-w-article-width mx-auto text-center mb-12">
        <div class="inline-flex items-center justify-center gap-2 mb-6">
            <span class="font-label-caps text-label-caps uppercase text-community-indigo tracking-widest">
                {{ $context === 'blog' ? 'Architecture' : 'Community' }}
            </span>
            <span class="text-surface-border mx-2">•</span>
            <span class="font-ui-small text-ui-small text-on-surface-variant">
                {{ $post->created_at->format('F d, Y') }}
            </span>
        </div>
        <h1 class="font-display-xl-mobile md:font-display-xl text-display-xl-mobile md:text-display-xl mb-8 leading-tight text-official-ink dark:text-paper-white">
            {{ $post->title }}
        </h1>
        <div class="flex items-center justify-center gap-4">
            <img
                alt="{{ $post->user->name }} profile"
                class="w-12 h-12 rounded-full object-cover border border-surface-border"
                src="{{ $post->user->avatar_url }}"
            />
            <div class="text-left">
                <div class="font-ui-medium text-ui-medium font-semibold text-official-ink dark:text-paper-white">{{ $post->user->name }}</div>
                <div class="font-ui-small text-ui-small text-on-surface-variant">{{ $post->reading_time_label }}</div>
            </div>
        </div>
    </header>

    <!-- Image (Hero for Blog, 1:1 for Community) -->
    @if($post->isBlog())
        <figure class="w-full max-w-container-max mx-auto mb-16">
            @if($post->cover_image_url)
                <img alt="Cover image" class="w-full h-[360px] md:h-[614px] object-cover rounded-DEFAULT border border-surface-border" src="{{ $post->cover_image_url }}" />
            @else
                <div class="w-full h-[360px] md:h-[614px] rounded-DEFAULT bg-gradient-to-br from-surface-container via-paper-white to-surface-container overflow-hidden border border-surface-border dark:from-primary-container dark:via-official-ink dark:to-primary-container"></div>
            @endif
            <figcaption class="font-ui-small text-ui-small text-on-surface-variant text-center mt-4 italic">
                {{ optional($post->category)->name ?? 'Editorial' }}
            </figcaption>
        </figure>
    @else
        @if($post->cover_image_url)
            <figure class="w-full max-w-article-width mx-auto mb-10 flex justify-center">
                <img alt="Image attachée" class="w-full max-w-[500px] aspect-square object-cover rounded-xl border border-surface-border shadow-sm" src="{{ $post->cover_image_url }}" />
            </figure>
        @endif
    @endif

    <!-- Article Body -->
    <article class="max-w-article-width mx-auto">
        <!-- AI Summary Indicator (if we had a summary on the model, we could show it here. For now, we only show it if a summary exists) -->
        @if(isset($post->summary))
            <div class="ai-summary p-6 rounded-lg mb-10 flex gap-4 items-start bg-[#EEF2FF] dark:bg-primary-container border border-community-indigo/20">
                <span translate="no" class="material-symbols-outlined text-community-indigo" data-icon="temp_preferences_custom">temp_preferences_custom</span>
                <div>
                    <h3 class="font-ui-medium text-ui-medium font-semibold text-community-indigo mb-2">Smart Summary</h3>
                    <p class="font-ui-small text-ui-small text-official-ink dark:text-paper-white">{{ $post->summary }}</p>
                </div>
            </div>
        @endif

        @php
            function renderMentions(string $text): string {
                $escaped = e($text);
                return preg_replace_callback(
                    '/@([a-zA-Z0-9_]{3,30})/',
                    fn($m) => '<a href="'.route('profile.show', $m[1]).'" class="text-community-indigo dark:text-secondary-fixed hover:underline font-medium">@'.$m[1].'</a>',
                    $escaped
                );
            }
        @endphp
        <div class="font-article-body text-article-body space-y-8 text-official-ink dark:text-paper-white leading-relaxed">
            {!! nl2br(renderMentions($post->body)) !!}
        </div>

        <!-- Meta + Actions Bar -->
        <div class="mt-16 pt-8 border-t border-surface-border flex flex-col gap-6">

            {{-- Category + type badge --}}
            <div class="flex gap-2 flex-wrap">
                @if($post->category)
                    <span class="px-3 py-1 bg-surface-container dark:bg-surface-container-low rounded-full font-label-caps text-label-caps text-on-surface-variant">
                        {{ $post->category->name }}
                    </span>
                @endif
                <span class="px-3 py-1 bg-surface-container dark:bg-surface-container-low rounded-full font-label-caps text-label-caps text-on-surface-variant">
                    {{ $post->type === 'BLOG' ? 'Blog Officiel' : 'Communauté' }}
                </span>
                <span class="px-3 py-1 bg-surface-container dark:bg-surface-container-low rounded-full font-label-caps text-label-caps text-on-surface-variant">
                    {{ $post->reading_time_label }}
                </span>
            </div>

            @auth
                {{-- Reactions --}}
                <div data-reaction-container class="flex flex-wrap gap-2 items-center">
                    @foreach(\App\Models\Reaction::ICONS as $type => $icon)
                        @php
                            $count = $post->reactions->where('type', $type)->count();
                            $hasReacted = auth()->check() ? $post->reactions->where('user_id', auth()->id())->where('type', $type)->isNotEmpty() : false;
                        @endphp
                        <button
                            type="button"
                            data-reactable-type="post"
                            data-reactable-id="{{ $post->id }}"
                            data-reaction-type="{{ $type }}"
                            class="inline-flex items-center gap-1 px-3 py-2 rounded-full transition-colors border border-surface-border dark:border-transparent group {{ $hasReacted ? 'bg-community-indigo/10 text-community-indigo' : 'bg-surface-container dark:bg-surface-container-low hover:bg-surface-dim dark:hover:bg-primary-container' }}"
                        >
                            <span translate="no" class="material-symbols-outlined text-[18px] {{ $hasReacted ? 'text-community-indigo' : 'group-hover:text-community-indigo' }}" data-icon="{{ $icon }}" style="{{ $hasReacted ? 'font-variation-settings: \'FILL\' 1;' : '' }}">{{ $icon }}</span>
                            <span data-reaction-count="{{ $type }}" class="font-ui-small text-ui-small font-medium {{ $hasReacted ? 'text-community-indigo' : 'text-on-surface-variant group-hover:text-community-indigo' }}">{{ $count }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Action buttons --}}
                <div class="flex flex-wrap gap-3 items-center">
                    {{-- Bookmark --}}
                    <button
                        type="button"
                        data-bookmark-post-id="{{ $post->id }}"
                        class="flex items-center gap-2 px-4 py-2 hover:bg-surface-dim dark:hover:bg-primary-container transition-colors rounded-full font-ui-small text-ui-small {{ $isBookmarked ?? false ? 'bg-community-indigo/10 text-community-indigo' : 'bg-surface-container dark:bg-surface-container-low text-official-ink dark:text-paper-white' }}"
                    >
                        <span translate="no" class="material-symbols-outlined text-[18px]" data-icon="bookmark" style="{{ $isBookmarked ?? false ? 'font-variation-settings: \'FILL\' 1;' : '' }}">bookmark</span> 
                        <span data-bookmark-text>{{ $isBookmarked ?? false ? 'Enregistré' : 'Enregistrer' }}</span>
                    </button>

                    {{-- Share --}}
                    <button
                        type="button"
                        onclick="navigator.clipboard?.writeText(location.href).then(() => { this.querySelector('span.label').textContent='Copié !' ; setTimeout(()=>this.querySelector('span.label').textContent='Partager',2000) });"
                        class="flex items-center gap-2 px-4 py-2 bg-surface-container dark:bg-surface-container-low hover:bg-surface-dim dark:hover:bg-primary-container transition-colors rounded-full font-ui-small text-ui-small text-official-ink dark:text-paper-white"
                    >
                        <span translate="no" class="material-symbols-outlined text-[18px]">share</span>
                        <span class="label">Partager</span>
                    </button>

                    {{-- Edit — author or admin (CDC §6.1) --}}
                    @can('update', $post)
                        <a
                            href="{{ $post->isBlog() ? route('blog.edit', $post) : route('community.edit', $post) }}"
                            class="flex items-center gap-2 px-4 py-2 bg-surface-container dark:bg-surface-container-low hover:bg-community-indigo/10 dark:hover:bg-secondary-fixed/10 transition-colors rounded-full font-ui-small text-ui-small text-community-indigo dark:text-secondary-fixed"
                        >
                            <span translate="no" class="material-symbols-outlined text-[18px]">edit</span> Modifier
                        </a>
                    @endcan

                    {{-- Delete — author or admin (CDC §6.1) --}}
                    @can('delete', $post)
                        <form method="POST" action="{{ $post->isBlog() ? route('blog.destroy', $post) : route('community.destroy', $post) }}" onsubmit="return confirm('Supprimer cette publication ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-error-container dark:bg-on-error-container/10 hover:bg-reaction-red/10 transition-colors rounded-full font-ui-small text-ui-small text-on-error-container dark:text-reaction-red">
                                <span translate="no" class="material-symbols-outlined text-[18px]">delete</span> Supprimer
                            </button>
                        </form>
                    @endcan

                    {{-- Report — other users only (CDC §7.1) --}}
                    @cannot('update', $post)
                        <button
                            type="button"
                            onclick="openReportModal('post', {{ $post->id }})"
                            class="flex items-center gap-2 px-4 py-2 bg-surface-container dark:bg-surface-container-low hover:bg-surface-dim dark:hover:bg-primary-container transition-colors rounded-full font-ui-small text-ui-small text-on-surface-variant"
                        >
                            <span translate="no" class="material-symbols-outlined text-[18px]">flag</span> Signaler
                        </button>
                    @endcannot
                </div>
            @endauth
        </div>
    </article>

    <!-- Community Section (Comments) -->
    <section 
        class="max-w-article-width mx-auto mt-20 bg-surface-container-low dark:bg-primary-container/20 p-8 rounded-lg"
        data-realtime-comments
        data-post-id="{{ $post->id }}"
    >
        <h3 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg mb-8 text-official-ink dark:text-paper-white">
            Discussion
        </h3>

        <!-- Comment Input -->
        @auth
            <div class="mb-12 flex gap-4">
                <img
                    alt="Current user"
                    class="w-10 h-10 rounded-full object-cover"
                    src="{{ auth()->user()->avatar_url }}"
                />
                <form method="POST" action="{{ route('comments.store', $post) }}" class="flex-grow">
                    @csrf
                    <input type="hidden" name="parent_id" value="">
                    <textarea
                        data-mentions
                        name="body"
                        class="w-full bg-paper-white dark:bg-official-ink border border-surface-border dark:border-surface-tint rounded-lg p-4 font-ui-small text-ui-small focus:outline-none focus:border-community-teal focus:ring-1 focus:ring-community-teal resize-none h-24 transition-colors text-official-ink dark:text-paper-white"
                        placeholder="Ajoutez votre commentaire… Tapez @ pour mentionner un membre."
                    ></textarea>
                    <div class="mt-2 flex justify-end">
                        <button class="bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink px-6 py-2 rounded-full font-ui-medium text-ui-medium hover:opacity-90 transition-opacity" type="submit">
                            Commenter
                        </button>
                    </div>
                </form>
            </div>
        @endauth

        <!-- Comment Thread -->
        <div class="space-y-8" data-comments-root>
            @forelse($post->rootComments as $comment)
                @include('comments._comment', ['comment' => $comment, 'post' => $post, 'depth' => 0])
            @empty
                <p class="text-on-surface-variant font-ui-small" data-comments-empty>Aucun commentaire pour l’instant. Soyez le premier à partager vos pensées !</p>
            @endforelse
        </div>
    </section>
</main>

{{-- =========================================================
     REPORT MODAL (CDC §7.1)
     ========================================================= --}}
@auth
<div
    id="report-modal"
    class="hidden fixed inset-0 z-[200] flex items-center justify-center p-4 bg-official-ink/50 dark:bg-black/70 backdrop-blur-sm"
    onclick="if(event.target===this) closeReportModal()"
>
    <div class="bg-paper-white dark:bg-official-ink rounded-2xl shadow-2xl w-full max-w-md p-6 flex flex-col gap-5">
        <div class="flex items-center justify-between">
            <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-official-ink dark:text-paper-white">Signaler un contenu</h2>
            <button onclick="closeReportModal()" class="p-2 rounded-full hover:bg-surface-container dark:hover:bg-primary-container transition-colors">
                <span translate="no" class="material-symbols-outlined text-on-surface-variant">close</span>
            </button>
        </div>

        <p class="font-ui-small text-ui-small text-on-surface-variant dark:text-surface-dim">
            Sélectionnez le motif du signalement. Il sera transmis à l'équipe de modération de manière confidentielle.
        </p>

        <form id="report-form" method="POST" action="{{ route('reports.store') }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="reportable_type" id="report-type">
            <input type="hidden" name="reportable_id"   id="report-id">

            <fieldset class="flex flex-col gap-3">
                @foreach([
                    'spam'            => 'Spam',
                    'harassment'      => 'Harcèlement',
                    'offensive'       => 'Contenu offensant',
                    'misinformation'  => 'Contenu faux / désinformation',
                    'other'           => 'Autre',
                ] as $value => $label)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input
                            type="radio"
                            name="reason"
                            value="{{ $value }}"
                            class="text-community-indigo focus:ring-community-indigo border-surface-border dark:border-surface-tint"
                            required
                        >
                        <span class="font-ui-medium text-ui-medium text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </fieldset>

            <div class="flex gap-3 justify-end pt-2">
                <button
                    type="button"
                    onclick="closeReportModal()"
                    class="px-6 py-2 rounded-full border border-surface-border dark:border-surface-tint font-ui-medium text-ui-medium text-on-surface-variant hover:bg-surface-container dark:hover:bg-primary-container transition-colors"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="px-6 py-2 rounded-full bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink font-ui-medium text-ui-medium hover:opacity-90 transition-opacity"
                >
                    Soumettre
                </button>
            </div>
        </form>
    </div>
</div>
@endauth

<script>
// ─── Report Modal ──────────────────────────────────────────────
function openReportModal(type, id) {
    document.getElementById('report-type').value = type;
    document.getElementById('report-id').value   = id;
    document.getElementById('report-modal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
function closeReportModal() {
    document.getElementById('report-modal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// ─── Toggle edit form on comments ─────────────────────────────
function toggleEditForm(id) {
    const body = document.getElementById('comment-body-' + id);
    const form = document.getElementById('edit-form-' + id);
    if (!body || !form) return;
    body.classList.toggle('hidden');
    form.classList.toggle('hidden');
    if (!form.classList.contains('hidden')) {
        form.querySelector('textarea')?.focus();
    }
}

// ─── @mention autocomplete for ALL [data-mentions] textareas ──
document.addEventListener('DOMContentLoaded', () => {
    function initMentions(ta) {
        if (ta.dataset.mentionsInit) return;
        ta.dataset.mentionsInit = '1';

        let drop = document.createElement('ul');
        drop.className = 'hidden absolute z-50 bg-paper-white dark:bg-official-ink border border-surface-border dark:border-surface-tint rounded-xl shadow-xl py-1 min-w-[220px] max-h-[200px] overflow-y-auto';
        ta.parentElement.style.position = 'relative';
        ta.parentElement.appendChild(drop);

        let users = [], query = null, start = -1, active = -1, timer = null;

        function show(list) {
            users = list; drop.innerHTML = '';
            if (!list.length) { hide(); return; }
            list.forEach((u, i) => {
                const li = document.createElement('li');
                li.className = 'flex items-center gap-3 px-4 py-2 cursor-pointer hover:bg-surface-container dark:hover:bg-primary-container transition-colors';
                li.innerHTML = `<div class="w-6 h-6 rounded-full bg-surface-container-high overflow-hidden flex-shrink-0">${u.avatar_url ? `<img src="${u.avatar_url}" class="w-full h-full object-cover">` : '<span translate="no" class="material-symbols-outlined text-[14px] text-on-surface-variant">person</span>'}</div><div><div class="font-ui-small text-ui-small text-official-ink dark:text-paper-white">${u.name}</div><div class="font-ui-small text-[11px] text-on-surface-variant">@${u.username}</div></div>`;
                li.addEventListener('click', () => insert(u.username));
                drop.appendChild(li);
            });
            drop.classList.remove('hidden');
            setActive(0);
        }
        function hide() { drop.classList.add('hidden'); drop.innerHTML=''; query=null; start=-1; active=-1; users=[]; }
        function setActive(i) { drop.querySelectorAll('li').forEach((l,j)=>l.classList.toggle('bg-surface-container',j===i)); active=i; }
        function insert(username) {
            const before = ta.value.substring(0, start);
            const after  = ta.value.substring(ta.selectionStart);
            ta.value = before + '@' + username + ' ' + after;
            const p = (before + '@' + username + ' ').length;
            ta.setSelectionRange(p, p); ta.focus(); hide();
        }

        ta.addEventListener('input', () => {
            const text = ta.value.substring(0, ta.selectionStart);
            const m = text.match(/@([\w]*)$/);
            if (m) {
                query = m[1]; start = ta.selectionStart - m[0].length;
                clearTimeout(timer);
                timer = setTimeout(() => {
                    fetch(`/users/search?q=${encodeURIComponent(query)}`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
                        .then(r=>r.json()).then(d=>show(d.users??d)).catch(hide);
                }, 180);
            } else hide();
        });

        ta.addEventListener('keydown', e => {
            if (drop.classList.contains('hidden')) return;
            const items = drop.querySelectorAll('li');
            if (e.key==='ArrowDown'){ e.preventDefault(); setActive((active+1)%items.length); }
            else if (e.key==='ArrowUp'){ e.preventDefault(); setActive((active-1+items.length)%items.length); }
            else if ((e.key==='Enter'||e.key==='Tab') && active>=0 && users[active]){ e.preventDefault(); insert(users[active].username); }
            else if (e.key==='Escape') hide();
        });

        document.addEventListener('click', e => { if (!drop.contains(e.target) && e.target!==ta) hide(); });
    }

    document.querySelectorAll('[data-mentions]').forEach(initMentions);
});

// ─── Reactions & Bookmarks ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Reactions
    document.querySelectorAll('[data-reactable-type]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const type = btn.dataset.reactableType;
            const id = btn.dataset.reactableId;
            const reaction = btn.dataset.reactionType;
            
            try {
                const res = await fetch('{{ route("reactions.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reactable_type: type, reactable_id: id, type: reaction })
                });
                
                if (res.ok) {
                    const data = await res.json();
                    
                    const container = btn.closest('[data-reaction-container]');
                    if (container) {
                        container.querySelectorAll('[data-reactable-type]').forEach(b => {
                            const rType = b.dataset.reactionType;
                            const countSpan = b.querySelector('[data-reaction-count]');
                            const iconSpan = b.querySelector('.material-symbols-outlined');
                            
                            if (countSpan && data.counts[rType] !== undefined) {
                                countSpan.textContent = data.counts[rType];
                            }
                            
                            if (data.user_reaction === rType) {
                                b.classList.add('bg-community-indigo/10', 'text-community-indigo');
                                b.classList.remove('bg-surface-container', 'dark:bg-surface-container-low', 'hover:bg-surface-dim', 'dark:hover:bg-primary-container');
                                countSpan.classList.add('text-community-indigo');
                                countSpan.classList.remove('text-on-surface-variant', 'group-hover:text-community-indigo');
                                iconSpan.classList.add('text-community-indigo');
                                iconSpan.classList.remove('group-hover:text-community-indigo');
                                iconSpan.style.fontVariationSettings = "'FILL' 1";
                            } else {
                                b.classList.remove('bg-community-indigo/10', 'text-community-indigo');
                                b.classList.add('bg-surface-container', 'dark:bg-surface-container-low', 'hover:bg-surface-dim', 'dark:hover:bg-primary-container');
                                countSpan.classList.remove('text-community-indigo');
                                countSpan.classList.add('text-on-surface-variant', 'group-hover:text-community-indigo');
                                iconSpan.classList.remove('text-community-indigo');
                                iconSpan.classList.add('group-hover:text-community-indigo');
                                iconSpan.style.fontVariationSettings = "";
                            }
                        });
                    }
                } else if (res.status === 401) {
                    window.location.href = '{{ route("login") }}';
                }
            } catch (e) {
                console.error(e);
            }
        });
    });

    // Bookmarks
    document.querySelectorAll('[data-bookmark-post-id]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.bookmarkPostId;
            try {
                const res = await fetch(`/bookmarks/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (res.ok) {
                    const data = await res.json();
                    const iconSpan = btn.querySelector('.material-symbols-outlined');
                    const textSpan = btn.querySelector('[data-bookmark-text]');
                    
                    if (data.bookmarked) {
                        btn.classList.add('bg-community-indigo/10', 'text-community-indigo');
                        btn.classList.remove('bg-surface-container', 'dark:bg-surface-container-low', 'text-official-ink', 'dark:text-paper-white');
                        iconSpan.style.fontVariationSettings = "'FILL' 1";
                        if (textSpan) textSpan.textContent = 'Enregistré';
                    } else {
                        btn.classList.remove('bg-community-indigo/10', 'text-community-indigo');
                        btn.classList.add('bg-surface-container', 'dark:bg-surface-container-low', 'text-official-ink', 'dark:text-paper-white');
                        iconSpan.style.fontVariationSettings = "";
                        if (textSpan) textSpan.textContent = 'Enregistrer';
                    }
                } else if (res.status === 401) {
                    window.location.href = '{{ route("login") }}';
                }
            } catch (e) {
                console.error(e);
            }
        });
    });
});
</script>
@endsection
