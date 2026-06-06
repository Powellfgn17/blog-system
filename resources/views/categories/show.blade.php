@extends('layouts.app')

@section('content')
<section class="max-w-container-max mx-auto px-0 md:px-margin-desktop py-4 flex flex-col gap-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-surface-border pb-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span translate="no" class="material-symbols-outlined text-community-teal text-xl">folder_open</span>
                <span class="font-label-caps text-xs uppercase text-on-surface-variant tracking-widest">Catégorie</span>
            </div>
            <h1 class="font-display-xl-mobile md:font-display-xl font-extrabold text-official-ink dark:text-paper-white tracking-tight">{{ $category->name }}</h1>
            <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim mt-2">{{ $posts->total() }} publication(s) dans cette catégorie</p>
        </div>
        <div class="md:ml-auto">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('blog.index') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-community-indigo dark:text-surface-dim dark:hover:text-secondary-fixed transition-colors font-ui-medium text-ui-medium font-semibold group border border-surface-border dark:border-surface-tint rounded-full px-4 py-2 hover:bg-surface-container-low dark:hover:bg-primary-container">
                <span translate="no" class="material-symbols-outlined text-[20px] transform group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Retour
            </a>
        </div>
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
                    </div>
                    <h3 class="font-headline-lg-mobile text-xl font-bold text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors leading-tight line-clamp-2">
                        {{ $post->title }}
                    </h3>
                    <p class="font-article-body text-sm text-on-surface-variant dark:text-surface-container-highest line-clamp-3">
                        {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 150) }}
                    </p>
                    <div class="mt-auto pt-4 border-t border-surface-border dark:border-surface-tint flex items-center gap-3">
                        <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="w-8 h-8 rounded-full object-cover border border-surface-border">
                        <div class="flex flex-col">
                            <span class="font-ui-small text-xs font-semibold text-official-ink dark:text-paper-white">{{ $post->user->name }}</span>
                            <span class="font-ui-small text-[10px] text-on-surface-variant dark:text-surface-dim">{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-surface-container-low dark:bg-primary-container/20 rounded-2xl border border-dashed border-surface-border dark:border-surface-tint">
                <span translate="no" class="material-symbols-outlined text-[64px] text-surface-tint dark:text-on-surface-variant mb-4">article</span>
                <h3 class="font-headline-lg-mobile text-2xl font-bold text-official-ink dark:text-paper-white mb-2">Aucune publication</h3>
                <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim max-w-md">Il n'y a pas encore de publications dans cette catégorie. Revenez plus tard !</p>
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
