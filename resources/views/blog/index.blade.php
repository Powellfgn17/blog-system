@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-12 md:gap-24 w-full">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-l-[4px] border-official-ink pl-4">
        <div>
            <h1 class="font-display-xl-mobile md:font-display-xl font-extrabold text-official-ink tracking-tight dark:text-paper-white">Thesaurus</h1>
            <p class="font-ui-medium text-ui-medium text-on-surface-variant mt-2 dark:text-surface-dim">Official Blog — articles éditoriaux et analyses.</p>
        </div>
        <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4">
            <form action="{{ route('search') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                <input type="text" name="q" class="w-full sm:w-64 rounded-DEFAULT border-surface-border bg-paper-white dark:bg-official-ink dark:text-paper-white dark:border-surface-tint focus:border-community-teal focus:ring-1 focus:ring-community-teal font-ui-small text-ui-small" placeholder="Rechercher...">
                <button class="px-4 py-2 rounded-DEFAULT border border-surface-border dark:border-surface-tint hover:bg-surface-container dark:hover:bg-primary-container transition-colors font-ui-small text-ui-small text-official-ink dark:text-paper-white">
                    <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;">search</span>
                </button>
            </form>
            @auth
                @if(auth()->user()->is_admin)
                    <a href="{{ route('blog.create') }}" class="whitespace-nowrap px-4 py-2 rounded-DEFAULT bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink font-ui-medium text-ui-medium hover:opacity-90 transition-opacity">Nouveau post</a>
                @endif
            @endauth
        </div>
    </div>

    @if($featured)
        <!-- Featured Post Section (Asymmetrical Editorial Layout) -->
        <a href="{{ route('blog.show', $featured) }}" class="w-full flex flex-col lg:flex-row gap-gutter items-center group cursor-pointer border-l-[4px] border-official-ink pl-4 md:pl-8">
            <div class="w-full lg:w-5/12 flex flex-col gap-6 order-2 lg:order-1">
                <div class="flex items-center gap-3">
                    <span class="font-label-caps text-label-caps uppercase tracking-widest text-official-ink dark:text-paper-white border border-surface-border dark:border-surface-tint px-3 py-1 rounded-DEFAULT">Featured</span>
                    <span class="font-ui-small text-ui-small text-on-surface-variant dark:text-surface-dim">{{ optional($featured->category)->name ?? 'Architecture & Design' }}</span>
                </div>
                <h2 class="font-display-xl-mobile md:font-display-xl text-display-xl-mobile md:text-display-xl text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors duration-300">
                    {{ $featured->title }}
                </h2>
                <p class="font-article-body text-article-body text-on-surface-variant dark:text-surface-container-highest max-w-[500px] line-clamp-3">
                    {{ \Illuminate\Support\Str::limit(strip_tags($featured->body), 260) }}
                </p>
                <div class="mt-4 flex items-center gap-2 font-ui-medium text-ui-medium text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors">
                    <span>Read Full Editorial</span>
                    <span class="material-symbols-outlined transform group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </div>
            <div class="w-full lg:w-7/12 aspect-[4/3] md:aspect-video lg:aspect-[16/10] order-1 lg:order-2 overflow-hidden rounded-lg bg-surface-container dark:bg-primary-container">
                @if($featured->cover_image_url ?? false)
                    <img alt="Featured article header image" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-in-out mix-blend-multiply dark:mix-blend-normal opacity-90" src="{{ $featured->cover_image_url }}"/>
                @else
                    <div class="w-full h-full transform group-hover:scale-105 transition-transform duration-700 ease-in-out bg-gradient-to-br from-surface-container via-paper-white to-surface-container dark:from-primary-container dark:via-official-ink dark:to-primary-container"></div>
                @endif
            </div>
        </a>
    @endif

    <!-- Divider -->
    <hr class="border-t border-surface-border dark:border-surface-tint w-full max-w-[200px] mx-auto"/>

    <!-- Recent Articles Bento Grid -->
    <section class="w-full flex flex-col gap-12">
        <div class="flex items-baseline justify-between w-full">
            <h2 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-official-ink dark:text-paper-white border-l-[4px] border-official-ink pl-4">
                Latest Publications
            </h2>
            <a class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim hover:text-community-indigo dark:hover:text-secondary-fixed transition-colors" href="#">View All Archive</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter auto-rows-[minmax(300px,auto)]">
            @php($others = $posts->slice(1))

            @foreach($others as $index => $post)
                @if($loop->index === 0)
                    <!-- Bento Item 1: Large Image Focus (Spans 8 cols) -->
                    <a href="{{ route('blog.show', $post) }}" class="md:col-span-8 flex flex-col group cursor-pointer relative overflow-hidden rounded-lg bg-surface-container-low dark:bg-primary-container/30 border border-surface-border dark:border-surface-tint">
                        <div class="w-full h-[300px] md:h-[400px] overflow-hidden bg-surface-container dark:bg-primary-container">
                            @if($post->cover_image_url ?? false)
                                <img alt="Cover" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" src="{{ $post->cover_image_url }}"/>
                            @else
                                <div class="w-full h-full transform group-hover:scale-105 transition-transform duration-500 bg-gradient-to-br from-surface-container via-paper-white to-surface-container dark:from-primary-container dark:via-official-ink dark:to-primary-container"></div>
                            @endif
                        </div>
                        <div class="p-8 flex flex-col gap-4 bg-paper-white dark:bg-official-ink relative z-10 -mt-12 mx-4 md:mx-8 mb-4 md:mb-8 rounded-DEFAULT shadow-[0px_4px_20px_rgba(15,23,42,0.05)] border border-surface-border dark:border-surface-tint">
                            <div class="flex gap-2">
                                <span class="font-label-caps text-label-caps text-on-surface-variant dark:text-surface-dim tracking-wider">{{ optional($post->category)->name ?? 'Editorial' }}</span>
                                <span class="text-surface-border">•</span>
                                <span class="font-ui-small text-ui-small text-on-surface-variant dark:text-surface-dim">{{ $post->created_at->format('M d, Y') }}</span>
                            </div>
                            <h3 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors leading-tight">
                                {{ $post->title }}
                            </h3>
                            <p class="font-article-body text-article-body text-on-surface-variant dark:text-surface-container-highest line-clamp-3 mt-4">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 200) }}
                            </p>
                        </div>
                    </a>
                @elseif($loop->index === 1)
                    <!-- Bento Item 2: Typographic Focus (Spans 4 cols) -->
                    <a href="{{ route('blog.show', $post) }}" class="md:col-span-4 flex flex-col justify-between p-8 bg-surface-container-low dark:bg-primary-container/20 rounded-lg border border-surface-border dark:border-surface-tint group cursor-pointer hover:border-official-ink dark:hover:border-paper-white transition-colors">
                        <div class="flex flex-col gap-4">
                            <span class="font-label-caps text-label-caps text-community-indigo dark:text-secondary-fixed tracking-wider">{{ optional($post->category)->name ?? 'Editorial Opinion' }}</span>
                            <h3 class="font-headline-lg-mobile text-headline-lg-mobile text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors leading-snug">
                                {{ $post->title }}
                            </h3>
                            <p class="font-article-body text-article-body text-on-surface-variant dark:text-surface-container-highest line-clamp-4">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 180) }}
                            </p>
                        </div>
                        <div class="mt-8 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-surface-variant overflow-hidden">
                                <img alt="Author portrait" class="w-full h-full object-cover grayscale mix-blend-multiply dark:mix-blend-normal" src="{{ $post->user->avatar_url }}"/>
                            </div>
                            <div>
                                <p class="font-ui-small text-ui-small font-bold text-official-ink dark:text-paper-white">{{ $post->user->name }}</p>
                                <p class="font-label-caps text-label-caps text-on-surface-variant dark:text-surface-dim">Author</p>
                            </div>
                        </div>
                    </a>
                @elseif($loop->index === 2)
                    <!-- Bento Item 3: Deep Dive / Long Form (Spans 6 cols) -->
                    <a href="{{ route('blog.show', $post) }}" class="md:col-span-6 flex flex-col p-8 bg-paper-white dark:bg-official-ink rounded-lg border-l-[4px] border-surface-border dark:border-surface-tint hover:border-official-ink dark:hover:border-paper-white transition-colors shadow-[0px_4px_20px_rgba(15,23,42,0.02)] group cursor-pointer">
                        <span class="font-label-caps text-label-caps text-on-surface-variant dark:text-surface-dim tracking-wider mb-4">Deep Dive</span>
                        <h3 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-official-ink dark:text-paper-white mb-4 group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors">
                            {{ $post->title }}
                        </h3>
                        <p class="font-article-body text-article-body text-on-surface-variant dark:text-surface-container-highest mb-6 flex-grow line-clamp-4">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 220) }}
                        </p>
                        <div class="flex items-center gap-2 font-ui-medium text-ui-medium text-official-ink dark:text-paper-white uppercase tracking-wide text-xs group-hover:underline">
                            Read Essay <span class="material-symbols-outlined text-[16px]">arrow_outward</span>
                        </div>
                    </a>
                @elseif($loop->index === 3)
                    <!-- Bento Item 4: Quote/Highlight (Spans 6 cols) -->
                    <a href="{{ route('blog.show', $post) }}" class="md:col-span-6 p-8 md:p-12 bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink rounded-lg flex flex-col justify-center items-center text-center relative overflow-hidden group cursor-pointer border border-transparent dark:border-surface-border">
                        <!-- Subtle background pattern using tailwind gradients -->
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white dark:from-black via-transparent to-transparent"></div>
                        <span class="material-symbols-outlined text-[48px] text-surface-tint dark:text-on-surface-variant mb-6 relative z-10" style="font-variation-settings: 'FILL' 1;">format_quote</span>
                        <blockquote class="font-display-xl-mobile text-display-xl-mobile md:font-display-xl md:text-display-xl font-extrabold leading-tight tracking-tight relative z-10 mb-8 max-w-lg mx-auto">
                            "{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 80, '...') }}"
                        </blockquote>
                        <p class="font-ui-medium text-ui-medium text-inverse-primary dark:text-on-primary-container relative z-10">From: <i>{{ $post->title }}</i></p>
                    </a>
                @else
                    <!-- Fallback Bento Item (Spans 4 cols) -->
                    <a href="{{ route('blog.show', $post) }}" class="md:col-span-4 flex flex-col justify-between p-8 bg-surface-container-low dark:bg-primary-container/20 rounded-lg border border-surface-border dark:border-surface-tint group cursor-pointer hover:border-official-ink dark:hover:border-paper-white transition-colors">
                        <div class="flex flex-col gap-4">
                            <span class="font-label-caps text-label-caps text-on-surface-variant dark:text-surface-dim tracking-wider">{{ optional($post->category)->name ?? 'Editorial' }}</span>
                            <h3 class="font-headline-lg-mobile text-headline-lg-mobile text-official-ink dark:text-paper-white group-hover:text-community-indigo dark:group-hover:text-secondary-fixed transition-colors leading-snug">
                                {{ $post->title }}
                            </h3>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>

        <div class="mt-8 flex justify-center w-full">
            {{ $posts->links() }}
        </div>
    </section>
</section>
@endsection
