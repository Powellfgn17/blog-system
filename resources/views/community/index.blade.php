@extends('layouts.app')

@section('content')
@php
    $contributors = $posts->getCollection()
        ->pluck('user')
        ->filter()
        ->unique('id')
        ->take(5);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 py-4">
    <!-- Main Content (Feed) -->
    <div class="lg:col-span-8 flex flex-col gap-6">
        <div class="flex items-end justify-between">
            <div>
                <h1 class="font-display text-[36px] md:text-[48px] leading-tight font-extrabold text-official-ink tracking-tight">Community Space</h1>
                <p class="text-on-surface-variant mt-2 text-sm">Publications et discussions de la communauté.</p>
            </div>
        </div>

        @auth
            <div class="bg-paper-white rounded-xl border border-surface-border p-6 shadow-[0px_4px_20px_rgba(15,23,42,0.05)] relative overflow-hidden group focus-within:border-community-teal transition-colors duration-300">
                <div class="absolute top-0 left-0 w-1 h-full bg-community-teal"></div>
                <div class="flex gap-4">
                    <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 border border-surface-border bg-surface-container">
                        <img alt="Current user avatar" class="w-full h-full object-cover" src="{{ auth()->user()->avatar_url }}"/>
                    </div>
                    <div class="flex-grow flex flex-col gap-4">
                        <a href="{{ route('community.create') }}" class="block">
                            <textarea class="w-full bg-transparent border-none focus:ring-0 p-0 resize-none font-ui-medium text-ui-medium text-on-surface placeholder:text-on-surface-variant min-h-[60px] cursor-pointer" placeholder="Quoi de neuf, communauté ?" rows="2" disabled></textarea>
                        </a>
                        <div class="flex justify-end items-center pt-4 border-t border-surface-border/50">
                            <a href="{{ route('community.create') }}" class="bg-community-indigo text-white font-ui-medium text-ui-medium px-6 py-2 rounded-lg hover:opacity-90 transition-opacity">Post</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-paper-white rounded-xl border border-surface-border p-6 shadow-[0px_4px_20px_rgba(15,23,42,0.05)]">
                <p class="text-on-surface-variant">Connecte-toi pour publier et interagir dans l'espace communautaire.</p>
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('login') }}" class="bg-community-indigo text-white px-5 py-2 rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Connexion</a>
                    <a href="{{ route('register') }}" class="border border-surface-border px-5 py-2 rounded-lg text-sm font-medium hover:bg-surface-container-low transition-colors">Inscription</a>
                </div>
            </div>
        @endauth

        <!-- Feed Cards -->
        @forelse($posts as $post)
            <article class="bg-paper-white rounded-xl border border-surface-border p-6 md:p-8 shadow-[0px_4px_20px_rgba(15,23,42,0.05)] hover:shadow-[0px_6px_24px_rgba(15,23,42,0.08)] transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-surface-border bg-surface-container">
                            <img alt="User avatar" class="w-full h-full object-cover" src="{{ $post->user->avatar_url }}"/>
                        </div>
                        <div>
                            <h3 class="font-ui-medium text-ui-medium text-on-surface leading-tight break-words">{{ $post->user->name }}</h3>
                            <span class="font-ui-small text-xs text-on-surface-variant">
                                {{ $post->created_at->diffForHumans() }} • 
                                @if($post->category)
                                    <a class="text-community-teal hover:underline font-semibold" href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                                @else
                                    <span class="text-slate-400">Sans catégorie</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="relative" data-dropdown>
                        <button type="button" data-dropdown-trigger class="text-on-surface-variant hover:text-official-ink p-1 rounded-full hover:bg-surface-container transition-colors">
                            <span translate="no" class="material-symbols-outlined pointer-events-none">more_horiz</span>
                        </button>
                        <div data-dropdown-menu class="hidden absolute right-0 mt-2 w-48 bg-paper-white border border-surface-border rounded-xl shadow-lg z-50 flex flex-col overflow-hidden">
                            @can('update', $post)
                                <a href="{{ route('community.edit', $post) }}" class="px-4 py-3 hover:bg-surface-container transition-colors font-ui-small text-ui-small text-official-ink flex items-center gap-2">
                                    <span translate="no" class="material-symbols-outlined text-[18px]">edit</span> Modifier
                                </a>
                            @endcan
                            @can('delete', $post)
                                <form method="POST" action="{{ route('community.destroy', $post) }}" onsubmit="return confirm('Supprimer cette publication ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full text-left px-4 py-3 hover:bg-error-container hover:text-on-error-container transition-colors font-ui-small text-ui-small text-reaction-red flex items-center gap-2">
                                        <span translate="no" class="material-symbols-outlined text-[18px]">delete</span> Supprimer
                                    </button>
                                </form>
                            @endcan
                            @auth
                                @cannot('update', $post)
                                    <button type="button" onclick="openReportModal('post', {{ $post->id }})" class="w-full text-left px-4 py-3 hover:bg-surface-container transition-colors font-ui-small text-ui-small text-official-ink flex items-center gap-2">
                                        <span translate="no" class="material-symbols-outlined text-[18px]">flag</span> Signaler
                                    </button>
                                @endcannot
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="pb-4">
                    <h2 class="font-display text-2xl font-bold text-official-ink leading-snug break-words mb-2">
                        <a class="hover:text-community-indigo transition-colors" href="{{ route('community.show', $post) }}">{{ $post->title }}</a>
                    </h2>
                    
                    @if($post->cover_image_url)
                        <a href="{{ route('community.show', $post) }}" class="block mb-4 overflow-hidden rounded-lg border border-surface-border w-full max-w-[400px]">
                            <img src="{{ $post->cover_image_url }}" alt="Image" class="w-full aspect-square object-cover hover:scale-105 transition-transform duration-300">
                        </a>
                    @endif

                    <p class="font-article text-base text-on-surface-variant break-words leading-7">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 260) }}</p>
                </div>

                <div class="py-3 border-t border-surface-border flex items-center justify-between bg-surface-container-low/50 -mx-6 -mb-6 px-6 rounded-b-xl">
                    <div class="flex items-center gap-1">
                        @php
                            $loveCount = $post->reactions->where('type', 'love')->count();
                            $hasLoved = auth()->check() ? $post->reactions->where('user_id', auth()->id())->where('type', 'love')->isNotEmpty() : false;
                        @endphp
                        <div class="relative reaction-pill" data-reaction-container>
                            <button
                                type="button"
                                data-reactable-type="post"
                                data-reactable-id="{{ $post->id }}"
                                data-reaction-type="love"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-full transition-colors group {{ $hasLoved ? 'bg-community-indigo/10 text-community-indigo' : 'bg-surface-container hover:bg-surface-dim' }}"
                            >
                                <span translate="no" class="material-symbols-outlined text-[16px] {{ $hasLoved ? 'text-community-indigo' : 'text-on-surface-variant group-hover:text-community-indigo' }}" style="{{ $hasLoved ? 'font-variation-settings: \'FILL\' 1;' : '' }}">favorite</span>
                                <span class="font-ui-medium text-xs font-semibold {{ $hasLoved ? 'text-community-indigo' : 'text-on-surface-variant group-hover:text-community-indigo' }}" data-reaction-count="love">{{ $loveCount }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-on-surface-variant font-ui-small text-xs">
                        <a href="{{ route('community.show', $post) }}" class="hover:text-community-teal flex items-center gap-1 transition-colors">
                            <span translate="no" class="material-symbols-outlined text-[18px]">chat_bubble</span>
                            <span>{{ $post->comments()->count() }} Réponses</span>
                        </a>
                        <button type="button" class="hover:text-community-teal flex items-center gap-1 transition-colors">
                            <span translate="no" class="material-symbols-outlined text-[18px]">share</span>
                            <span>Partager</span>
                        </button>
                    </div>
                </div>
            </article>
        @empty
            <div class="bg-white border border-surface-border rounded-xl p-8 text-center text-on-surface-variant shadow-sm">
                <span translate="no" class="material-symbols-outlined text-4xl text-slate-300 mb-2">forum</span>
                <p>Aucune publication communautaire pour le moment.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Right Sidebar -->
    <aside class="hidden lg:flex lg:col-span-4 flex-col gap-6">
        <!-- Trending Spaces -->
        <div class="bg-white rounded-xl p-6 border border-surface-border shadow-[0px_4px_20px_rgba(15,23,42,0.05)]">
            <h3 class="font-display text-sm font-bold text-official-ink mb-4 flex items-center gap-2 uppercase tracking-wider text-[11px]">
                <span translate="no" class="material-symbols-outlined text-[18px] text-community-teal">trending_up</span>
                Espaces Tendances
            </h3>
            <div class="flex flex-wrap gap-2">
                @forelse($categories->take(10) as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="px-3 py-1.5 bg-surface-container hover:bg-slate-200 rounded-full font-ui-small text-xs text-on-surface hover:text-community-indigo cursor-pointer transition-all break-words">
                        #{{ $category->name }}
                    </a>
                @empty
                    <span class="text-xs text-on-surface-variant">Aucune catégorie.</span>
                @endforelse
            </div>
        </div>

        <!-- Active Contributors -->
        <div class="relative bg-white rounded-xl p-6 border border-surface-border shadow-[0px_4px_20px_rgba(15,23,42,0.05)] overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-community-teal/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <h3 class="font-display text-sm font-bold text-official-ink mb-4 relative z-10 flex items-center gap-2 uppercase tracking-wider text-[11px]">
                <span translate="no" class="material-symbols-outlined text-[18px] text-community-indigo">groups</span>
                Contributeurs Actifs
            </h3>
            <ul class="space-y-4 relative z-10">
                @forelse($contributors as $u)
                    <li class="flex items-center gap-3">
                        <img class="w-9 h-9 rounded-full object-cover border border-surface-border shadow-sm" src="{{ $u->avatar_url }}" alt="avatar">
                        <div>
                            <p class="font-ui-medium text-sm text-official-ink font-semibold break-words leading-tight">{{ $u->name }}</p>
                            <p class="text-xs text-on-surface-variant font-mono mt-0.5">{{ '@' . $u->username }}</p>
                        </div>
                    </li>
                @empty
                    <li class="text-xs text-on-surface-variant">Aucune activité récente.</li>
                @endforelse
            </ul>
        </div>
    </aside>
</div>

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
// ─── Dropdown Menus ──────────────────────────────────────────────
document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-dropdown-trigger]');
    if (trigger) {
        const menu = trigger.nextElementSibling;
        const isHidden = menu.classList.contains('hidden');
        
        document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
        
        if (isHidden) {
            menu.classList.remove('hidden');
        }
        return;
    }
    
    if (!e.target.closest('[data-dropdown]')) {
        document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
    }
});

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

document.addEventListener('DOMContentLoaded', () => {
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
                                b.classList.remove('bg-surface-container', 'hover:bg-surface-dim');
                                countSpan.classList.add('text-community-indigo');
                                countSpan.classList.remove('text-on-surface-variant', 'group-hover:text-community-indigo');
                                iconSpan.classList.add('text-community-indigo');
                                iconSpan.classList.remove('text-on-surface-variant', 'group-hover:text-community-indigo');
                                iconSpan.style.fontVariationSettings = "'FILL' 1";
                            } else {
                                b.classList.remove('bg-community-indigo/10', 'text-community-indigo');
                                b.classList.add('bg-surface-container', 'hover:bg-surface-dim');
                                countSpan.classList.remove('text-community-indigo');
                                countSpan.classList.add('text-on-surface-variant', 'group-hover:text-community-indigo');
                                iconSpan.classList.remove('text-community-indigo');
                                iconSpan.classList.add('text-on-surface-variant', 'group-hover:text-community-indigo');
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
});
</script>
@endsection
