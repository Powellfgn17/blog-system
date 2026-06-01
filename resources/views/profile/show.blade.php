@extends('layouts.app')

@section('content')
<div class="w-full max-w-container-max mx-auto px-0 py-4">
    <!-- En-tête du profil -->
    <section class="mb-16 bg-white border border-surface-border rounded-xl p-8 shadow-[0px_4px_20px_rgba(15,23,42,0.05)]">
        <div class="flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left">
            <div class="w-32 h-32 md:w-40 md:h-40 flex-shrink-0">
                <img alt="Photo de profil de {{ $user->name }}" class="w-full h-full object-cover rounded-full border border-surface-border shadow-sm" src="{{ $user->avatar_url }}"/>
            </div>
            <div class="flex-grow flex flex-col justify-center min-h-[10rem] w-full">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <div>
                        <h1 class="font-display text-4xl font-extrabold text-official-ink">
                            {{ $user->name }}
                        </h1>
                        <p class="text-on-surface-variant font-mono text-sm mt-1">{{ '@' . $user->username }}</p>
                    </div>
                    @auth
                        @if(auth()->id() === $user->id)
                            <a href="{{ route('profile.edit') }}" class="self-center md:self-auto border border-surface-border text-official-ink font-ui-medium text-ui-medium px-6 py-2 rounded-lg hover:bg-surface-container hover:border-official-ink transition-colors">
                                Modifier le profil
                            </a>
                        @endif
                    @endauth
                </div>
                <p class="font-article text-lg leading-8 text-on-surface-variant max-w-2xl mb-6 break-words">
                    {{ $user->bio ?? "Cet utilisateur n'a pas encore rédigé de biographie." }}
                </p>
                <div class="flex justify-center md:justify-start gap-4">
                    <div class="bg-surface-container rounded-xl p-4 min-w-[120px] text-center shadow-sm">
                        <div class="font-display text-2xl font-bold text-official-ink">{{ $postsCount }}</div>
                        <div class="font-ui-small text-xs text-on-surface-variant uppercase tracking-widest mt-1">Publications</div>
                    </div>
                    <div class="bg-surface-container rounded-xl p-4 min-w-[120px] text-center shadow-sm">
                        <div class="font-display text-2xl font-bold text-official-ink">{{ $commentsCount }}</div>
                        <div class="font-ui-small text-xs text-on-surface-variant uppercase tracking-widest mt-1">Commentaires</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Corps de la page -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
        @php($isOwner = auth()->check() && auth()->id() === $user->id)
        
        <div class="{{ $isOwner ? 'lg:col-span-8' : 'lg:col-span-12' }} flex flex-col gap-8">
            <div class="flex items-center gap-2 mb-2 border-b border-surface-border pb-4">
                <span class="material-symbols-outlined text-community-indigo">forum</span>
                <h2 class="font-display text-2xl font-bold text-official-ink">Contributions Récentes</h2>
            </div>

            @forelse($posts as $post)
                <article class="bg-white border border-surface-border rounded-xl p-6 md:p-8 hover:shadow-[0px_4px_20px_rgba(15,23,42,0.05)] transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex gap-3 items-center">
                            <img src="{{ $post->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover" alt="avatar">
                            <span class="font-ui-small text-xs text-on-surface-variant">
                                {{ $post->created_at->diffForHumans() }} dans 
                                @if($post->category)
                                    <a class="text-community-teal hover:underline font-semibold" href="{{ route('categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                                @else
                                    <span class="text-slate-400">Sans catégorie</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <h3 class="font-display text-2xl font-bold text-official-ink mb-3">
                        <a href="{{ $post->isBlog() ? route('blog.show', $post) : route('community.show', $post) }}" class="hover:text-community-indigo transition-colors">{{ $post->title }}</a>
                    </h3>
                    <p class="font-article text-base text-on-surface-variant mb-6 line-clamp-3">
                        {{ \Illuminate\Support\Str::limit(strip_tags($post->body), 250) }}
                    </p>
                    <div class="flex gap-4 items-center pt-4 border-t border-surface-border">
                        <div class="flex items-center gap-1.5 text-on-surface-variant font-ui-small text-xs bg-surface-container rounded-full px-3 py-1">
                            <span class="material-symbols-outlined text-[16px] text-reaction-red">favorite</span> 
                            <span>{{ $post->reactions()->count() }}</span>
                        </div>
                        <a href="{{ $post->isBlog() ? route('blog.show', $post) : route('community.show', $post) }}" class="flex items-center gap-1.5 text-on-surface-variant hover:text-community-teal transition-colors font-ui-small text-xs">
                            <span class="material-symbols-outlined text-[16px]">chat_bubble</span> 
                            <span>{{ $post->comments()->count() }} réponses</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="bg-white border border-surface-border rounded-xl p-8 text-center text-on-surface-variant shadow-sm">
                    <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">article</span>
                    <p>Aucune contribution rédigée pour le moment.</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        </div>

        <!-- Sidebar des favoris (uniquement visible par le propriétaire) -->
        @if($isOwner)
            @php($bookmarks = auth()->user()->bookmarks()->with(['post.user', 'post.category'])->latest('created_at')->take(4)->get())
            <aside class="lg:col-span-4 bg-white border border-surface-border rounded-xl p-6 shadow-[0px_4px_20px_rgba(15,23,42,0.05)]">
                <div class="flex items-center gap-2 mb-6 border-b border-surface-border pb-4">
                    <span class="material-symbols-outlined text-official-ink">bookmark</span>
                    <h2 class="font-display text-xl font-bold text-official-ink">Vos Favoris</h2>
                </div>
                
                @if($bookmarks->isNotEmpty())
                    <ul class="flex flex-col gap-4">
                        @foreach($bookmarks as $bookmark)
                            @if($bookmark->post)
                                <li class="group cursor-pointer">
                                    <a class="block bg-surface-container-low hover:bg-surface-container p-4 rounded-lg border border-surface-border shadow-sm transition-all" href="{{ $bookmark->post->isBlog() ? route('blog.show', $bookmark->post) : route('community.show', $bookmark->post) }}">
                                        <h4 class="font-ui-medium text-sm text-official-ink group-hover:text-community-indigo transition-colors mb-1 line-clamp-2">{{ $bookmark->post->title }}</h4>
                                        <span class="font-ui-small text-xs text-on-surface-variant flex items-center gap-1 mt-2">
                                            <span class="material-symbols-outlined text-[14px]">link</span> 
                                            <span>{{ optional($bookmark->post->category)->name ?? 'Sans catégorie' }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <a href="{{ route('profile.bookmarks') }}" class="block w-full mt-6 text-center font-ui-small text-xs text-official-ink hover:text-community-teal transition-colors py-2 border border-surface-border rounded-lg bg-white hover:bg-surface-container-low">
                        Voir tous les favoris
                    </a>
                @else
                    <div class="text-center text-on-surface-variant py-6">
                        <span class="material-symbols-outlined text-3xl text-slate-300 mb-2">bookmark_border</span>
                        <p class="text-sm">Vous n'avez aucun favori pour le moment.</p>
                    </div>
                @endif
            </aside>
        @endif
    </div>
</div>
@endsection
