@extends('layouts.app')

@section('content')
<section class="max-w-container-max mx-auto px-0 md:px-margin-desktop py-4 flex flex-col gap-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-surface-border pb-6">
        <div>
            <h1 class="font-display-xl-mobile md:font-display-xl font-extrabold text-official-ink dark:text-paper-white tracking-tight">Recherche</h1>
            <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim mt-2">Résultats pour "{{ $query }}"</p>
        </div>
        
        <form action="{{ route('search') }}" method="GET" class="w-full md:w-auto relative group">
            <span translate="no" class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant group-focus-within:text-community-teal transition-colors">search</span>
            <input 
                type="text" 
                name="q" 
                value="{{ $query }}" 
                class="w-full md:w-80 pl-12 pr-4 py-3 rounded-full border border-surface-border bg-paper-white dark:bg-official-ink dark:text-paper-white dark:border-surface-tint focus:border-community-teal focus:ring-1 focus:ring-community-teal font-ui-small text-ui-small transition-all shadow-[0_2px_10px_rgba(15,23,42,0.02)]" 
                placeholder="Rechercher..."
            >
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
        @forelse($posts as $post)
            <a href="{{ $post->isBlog() ? route('blog.show', $post) : route('community.show', $post) }}" class="flex flex-col group cursor-pointer relative overflow-hidden rounded-xl bg-surface-container-low dark:bg-primary-container/30 border border-surface-border dark:border-surface-tint hover:shadow-[0px_8px_30px_rgba(15,23,42,0.08)] transition-all duration-300 transform hover:-translate-y-1">
                @if($post->cover_image_url)
                    <div class="w-full h-48 overflow-hidden bg-surface-container dark:bg-primary-container">
                        <img alt="Cover" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" src="{{ $post->cover_image_url }}"/>
                    </div>
                @endif
                <div class="p-6 flex flex-col gap-4 bg-paper-white dark:bg-official-ink flex-grow">
                    <div class="flex gap-2 flex-wrap">
                        <span class="font-label-caps text-[10px] uppercase text-paper-white {{ $post->isBlog() ? 'bg-official-ink' : 'bg-community-indigo' }} px-2 py-1 rounded-full tracking-wider">{{ $post->type }}</span>
                        @if($post->category)
                            <span class="font-label-caps text-[10px] uppercase text-on-surface-variant dark:text-surface-dim border border-surface-border px-2 py-1 rounded-full tracking-wider">{{ $post->category->name }}</span>
                        @endif
                    </div>
                    <h3 class="font-headline-lg-mobile text-xl font-bold text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors leading-tight line-clamp-2">
                        {{ $post->title }}
                    </h3>
                    <p class="font-article-body text-sm text-on-surface-variant dark:text-surface-container-highest line-clamp-3">
                        {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 150) }}
                    </p>
                    <div class="mt-auto pt-4 border-t border-surface-border dark:border-surface-tint flex items-center gap-3">
                        <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="w-8 h-8 rounded-full object-cover">
                        <div class="flex flex-col">
                            <span class="font-ui-small text-xs font-semibold text-official-ink dark:text-paper-white">{{ $post->user->name }}</span>
                            <span class="font-ui-small text-[10px] text-on-surface-variant dark:text-surface-dim">{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-surface-container-low dark:bg-primary-container/20 rounded-2xl border border-dashed border-surface-border dark:border-surface-tint">
                <span translate="no" class="material-symbols-outlined text-[64px] text-surface-tint dark:text-on-surface-variant mb-4" style="font-variation-settings: 'FILL' 1;">search_off</span>
                <h3 class="font-headline-lg-mobile text-2xl font-bold text-official-ink dark:text-paper-white mb-2">Aucun résultat trouvé</h3>
                <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim max-w-md">Nous n'avons trouvé aucune publication correspondant à "{{ $query }}". Essayez d'utiliser d'autres mots-clés ou vérifiez l'orthographe.</p>
                <a href="{{ route('blog.index') }}" class="mt-6 px-6 py-2 bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink rounded-full font-ui-medium text-ui-medium hover:opacity-90 transition-opacity">
                    Retour à l'accueil
                </a>
            </div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div class="mt-8 flex justify-center w-full">
            {{ $posts->links() }}
        </div>
    @endif
</section>
@endsection
