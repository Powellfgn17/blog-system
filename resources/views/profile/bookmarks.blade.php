@extends('layouts.app')

@section('content')
<section class="max-w-container-max mx-auto px-0 md:px-margin-desktop py-4 flex flex-col gap-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-surface-border pb-6">
        <div class="flex items-center gap-3">
            <span translate="no" class="material-symbols-outlined text-4xl text-official-ink dark:text-paper-white" style="font-variation-settings: 'FILL' 1;">bookmark</span>
            <div>
                <h1 class="font-display-xl-mobile md:font-display-xl font-extrabold text-official-ink dark:text-paper-white tracking-tight">Mes Favoris</h1>
                <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim mt-2">{{ $bookmarks->total() }} publication(s) enregistrée(s)</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
        @forelse($bookmarks as $bookmark)
            <article class="flex flex-col group relative overflow-hidden rounded-xl bg-surface-container-low dark:bg-primary-container/30 border border-surface-border dark:border-surface-tint hover:shadow-[0px_8px_30px_rgba(15,23,42,0.08)] transition-all duration-300">
                @if($bookmark->post->cover_image_url)
                    <a href="{{ $bookmark->post->isBlog() ? route('blog.show', $bookmark->post) : route('community.show', $bookmark->post) }}" class="w-full h-48 overflow-hidden bg-surface-container dark:bg-primary-container block">
                        <img alt="Cover" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" src="{{ $bookmark->post->cover_image_url }}"/>
                    </a>
                @endif
                <div class="p-6 flex flex-col gap-4 bg-paper-white dark:bg-official-ink flex-grow relative">
                    <div class="flex justify-between items-start">
                        <div class="flex gap-2 flex-wrap">
                            <span class="font-label-caps text-[10px] uppercase text-paper-white {{ $bookmark->post->isBlog() ? 'bg-official-ink' : 'bg-community-indigo' }} px-2 py-1 rounded-full tracking-wider">{{ $bookmark->post->type }}</span>
                            @if($bookmark->post->category)
                                <span class="font-label-caps text-[10px] uppercase text-on-surface-variant dark:text-surface-dim border border-surface-border px-2 py-1 rounded-full tracking-wider">{{ $bookmark->post->category->name }}</span>
                            @endif
                        </div>
                        <button 
                            type="button" 
                            data-bookmark-post-id="{{ $bookmark->post->id }}"
                            class="text-community-indigo hover:text-reaction-red transition-colors p-1 rounded-full hover:bg-surface-container dark:hover:bg-primary-container"
                            title="Retirer des favoris"
                        >
                            <span translate="no" class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">bookmark</span>
                        </button>
                    </div>
                    
                    <h3 class="font-headline-lg-mobile text-xl font-bold text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors leading-tight line-clamp-2 mt-2">
                        <a href="{{ $bookmark->post->isBlog() ? route('blog.show', $bookmark->post) : route('community.show', $bookmark->post) }}">{{ $bookmark->post->title }}</a>
                    </h3>
                    
                    <div class="mt-auto pt-4 border-t border-surface-border dark:border-surface-tint flex items-center gap-3">
                        <img src="{{ $bookmark->post->user->avatar_url }}" alt="{{ $bookmark->post->user->name }}" class="w-8 h-8 rounded-full object-cover border border-surface-border">
                        <div class="flex flex-col">
                            <span class="font-ui-small text-xs font-semibold text-official-ink dark:text-paper-white">{{ $bookmark->post->user->name }}</span>
                            <span class="font-ui-small text-[10px] text-on-surface-variant dark:text-surface-dim">Enregistré {{ $bookmark->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-surface-container-low dark:bg-primary-container/20 rounded-2xl border border-dashed border-surface-border dark:border-surface-tint">
                <span translate="no" class="material-symbols-outlined text-[64px] text-surface-tint dark:text-on-surface-variant mb-4">bookmark_border</span>
                <h3 class="font-headline-lg-mobile text-2xl font-bold text-official-ink dark:text-paper-white mb-2">Aucun favori enregistré</h3>
                <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim max-w-md">Vous n'avez pas encore enregistré de publications. Explorez le blog ou la communauté pour trouver du contenu intéressant.</p>
                <div class="flex gap-4 mt-6">
                    <a href="{{ route('blog.index') }}" class="px-6 py-2 bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink rounded-full font-ui-medium text-ui-medium hover:opacity-90 transition-opacity">
                        Explorer le Blog
                    </a>
                    <a href="{{ route('community.index') }}" class="px-6 py-2 border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white rounded-full font-ui-medium text-ui-medium hover:bg-surface-container dark:hover:bg-primary-container transition-colors">
                        Voir la Communauté
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($bookmarks->hasPages())
        <div class="mt-8 flex justify-center w-full">
            {{ $bookmarks->links() }}
        </div>
    @endif
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
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
                    if (!data.bookmarked) {
                        // Remove the article from DOM
                        const article = btn.closest('article');
                        article.style.opacity = '0';
                        setTimeout(() => article.remove(), 300);
                        
                        // Wait for a bit and check if we need to show empty state
                        setTimeout(() => {
                            if (document.querySelectorAll('article').length === 0) {
                                window.location.reload();
                            }
                        }, 350);
                    }
                }
            } catch (e) {
                console.error(e);
            }
        });
    });
});
</script>
@endsection
