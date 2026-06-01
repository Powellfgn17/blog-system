@extends('layouts.app')

@section('content')
<main class="w-full grid grid-cols-1 md:grid-cols-12 gap-gutter">
    <!-- Dashboard Header -->
    <div class="col-span-1 md:col-span-12 mb-8 flex flex-col md:flex-row justify-between items-start md:items-end border-b border-surface-border pb-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-official-ink dark:text-paper-white">Platform Overview</h1>
                <span class="font-ui-medium text-community-indigo border-l-2 border-surface-border pl-3 hidden md:inline-block">Admin Workspace</span>
            </div>
            <p class="font-ui-small text-ui-small text-on-surface-variant">Real-time metrics and moderation queue.</p>
        </div>
        <div class="hidden md:flex space-x-4 mt-4 md:mt-0">
            <a href="{{ route('admin.moderation') }}" class="font-ui-small text-ui-small border border-surface-border text-official-ink dark:text-paper-white px-4 py-2 rounded-DEFAULT hover:bg-surface-container dark:hover:bg-primary-container transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">flag</span>
                Modération
            </a>
            <a href="{{ route('admin.categories') }}" class="font-ui-small text-ui-small border border-surface-border text-official-ink dark:text-paper-white px-4 py-2 rounded-DEFAULT hover:bg-surface-container dark:hover:bg-primary-container transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">category</span>
                Catégories
            </a>
        </div>
    </div>

    <!-- High-level Stats (Bento Grid Style) -->
    <div class="col-span-1 md:col-span-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <!-- Stat Card: Users -->
        <div class="bg-surface-container-lowest dark:bg-primary-container/20 border border-surface-border dark:border-surface-tint p-6 rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-community-teal/5 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <h3 class="font-ui-small text-ui-small text-on-surface-variant mb-1">Total Users</h3>
            <p class="font-display-xl-mobile text-display-xl-mobile text-official-ink dark:text-paper-white font-extrabold">{{ number_format($stats['users']) }}</p>
            <div class="mt-4 flex items-center text-community-teal font-ui-small text-ui-small">
                <span class="material-symbols-outlined text-sm mr-1">group</span>
                <span>Active accounts</span>
            </div>
        </div>

        <!-- Stat Card: Blog Posts -->
        <div class="bg-surface-container-lowest dark:bg-primary-container/20 border border-surface-border dark:border-surface-tint p-6 rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-community-indigo/5 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <h3 class="font-ui-small text-ui-small text-on-surface-variant mb-1">Blog Posts</h3>
            <p class="font-display-xl-mobile text-display-xl-mobile text-official-ink dark:text-paper-white font-extrabold">{{ number_format($stats['posts_blog']) }}</p>
            <div class="mt-4 flex items-center text-community-indigo font-ui-small text-ui-small">
                <span class="material-symbols-outlined text-sm mr-1">article</span>
                <span>Official editorials</span>
            </div>
        </div>

        <!-- Stat Card: Community Posts -->
        <div class="bg-surface-container-lowest dark:bg-primary-container/20 border border-surface-border dark:border-surface-tint p-6 rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-community-teal/5 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
            <h3 class="font-ui-small text-ui-small text-on-surface-variant mb-1">Community Posts</h3>
            <p class="font-display-xl-mobile text-display-xl-mobile text-official-ink dark:text-paper-white font-extrabold">{{ number_format($stats['posts_community']) }}</p>
            <div class="mt-4 flex items-center text-community-teal font-ui-small text-ui-small">
                <span class="material-symbols-outlined text-sm mr-1">forum</span>
                <span>Community threads</span>
            </div>
        </div>

        <!-- Stat Card: Pending Reports (AI Summary style) -->
        <div class="bg-[#EEF2FF] dark:bg-primary-container border border-community-indigo/20 p-6 rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] relative overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-community-indigo" style="font-variation-settings: 'FILL' 1;">auto_awesome</span>
                    <h3 class="font-label-caps text-label-caps text-community-indigo tracking-widest uppercase">System Health</h3>
                </div>
                <p class="font-ui-small text-ui-small text-official-ink dark:text-paper-white mt-2">
                    <strong>{{ $stats['reports_pending'] }}</strong> pending reports.
                    <strong>{{ $stats['users_blocked'] }}</strong> blocked users.
                    <strong>{{ $stats['comments'] }}</strong> total comments.
                </p>
            </div>
            <a href="{{ route('admin.moderation') }}" class="text-left font-ui-small text-ui-small text-community-indigo font-medium hover:underline mt-4 flex items-center gap-1">
                View moderation queue <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area: Moderation Queue -->
    <div class="col-span-1 md:col-span-8 flex flex-col gap-8">
        <section class="bg-surface-container-lowest dark:bg-primary-container/10 border border-surface-border dark:border-surface-tint rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="font-ui-medium text-ui-medium font-bold text-official-ink dark:text-paper-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-reaction-red">flag</span>
                    Contenus signalés
                </h2>
                <span class="bg-surface-container dark:bg-primary-container px-3 py-1 rounded-full font-label-caps text-label-caps text-on-surface-variant">File : {{ $stats['reports_pending'] }}</span>
            </div>
            <div class="space-y-4">
                @forelse($flaggedItems as $item)
                    @php($content = $item['reportable'])
                    <div class="border border-surface-border dark:border-surface-tint rounded p-4 hover:border-community-indigo/30 transition-colors">
                        <div class="flex flex-col md:flex-row justify-between items-start mb-3 gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-ui-small text-ui-small font-bold text-official-ink dark:text-paper-white">{{ '@' . $content->user->username }}</span>
                                <span class="text-surface-variant hidden md:inline">•</span>
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($item['reports'] as $report)
                                        <span class="font-ui-small text-ui-small text-reaction-red bg-error-container dark:bg-on-error-container/20 px-2 py-0.5 rounded text-xs" title="{{ $report->user->name }}">
                                            {{ $report->reason_label }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <span class="font-ui-small text-ui-small text-on-surface-variant whitespace-nowrap">{{ $content->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="font-article-body text-article-body text-on-surface dark:text-surface-container-highest text-base leading-relaxed mb-4 pl-4 border-l-2 border-surface-variant dark:border-surface-tint">
                            "{{ \Illuminate\Support\Str::limit(strip_tags($content->body ?? $content->title ?? 'Contenu'), 220) }}"
                        </p>
                        <div class="flex justify-end gap-3 border-t border-surface-border dark:border-surface-tint pt-3">
                            @if($item['reports']->isNotEmpty())
                                <form method="POST" action="{{ route('admin.reports.ignore', $item['reports']->first()) }}">
                                    @csrf
                                    <button class="font-ui-small text-ui-small border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white px-4 py-2 rounded hover:bg-surface-container dark:hover:bg-primary-container transition-colors">Ignorer</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.content.destroy', ['type' => $content instanceof \App\Models\Post ? 'post' : 'comment', 'id' => $content->id]) }}">
                                @csrf @method('DELETE')
                                <button class="font-ui-small text-ui-small bg-reaction-red text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">Supprimer {{ class_basename($content) }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-on-surface-variant font-ui-small text-ui-small text-center py-8">Aucun signalement en attente.</p>
                @endforelse
            </div>
            @if(count($flaggedItems) > 0)
                <a href="{{ route('admin.moderation') }}" class="block text-center mt-6 font-ui-small text-ui-small text-community-indigo hover:underline">Voir toute la file d'attente</a>
            @endif
        </section>
    </div>

    <!-- Sidebar: Flagged Users -->
    <div class="col-span-1 md:col-span-4">
        <section class="bg-[#F1F5F9] dark:bg-primary-container/20 border border-surface-border dark:border-surface-tint rounded-lg p-6 h-full">
            <h2 class="font-ui-medium text-ui-medium font-bold text-official-ink dark:text-paper-white flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-community-indigo">group</span>
                Utilisateurs signalés
            </h2>
            <div class="space-y-4">
                @forelse($flaggedUsers as $flag)
                    @php($user = $flag['user'])
                    <div class="bg-surface-container-lowest dark:bg-official-ink border border-surface-border dark:border-surface-tint rounded p-3 flex flex-col md:flex-row items-start md:items-center justify-between shadow-sm gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-surface-variant overflow-hidden flex-shrink-0">
                                <img src="{{ $user->avatar_url }}" alt="User avatar" class="w-full h-full object-cover"/>
                            </div>
                            <div>
                                <p class="font-ui-medium text-ui-medium text-official-ink dark:text-paper-white text-sm truncate max-w-[120px]">{{ '@' . $user->username }}</p>
                                <p class="font-ui-small text-ui-small text-reaction-red text-xs">{{ $flag['reports_count'] }} signalement(s)</p>
                            </div>
                        </div>
                        <div class="self-end md:self-auto">
                            @if($user->is_blocked)
                                <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                                    @csrf
                                    <button class="font-ui-small text-xs border border-surface-border px-2 py-1 rounded text-on-surface-variant hover:bg-surface-container">Débloquer</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.block', $user) }}">
                                    @csrf
                                    <button class="font-ui-small text-xs bg-error-container text-reaction-red px-2 py-1 rounded hover:bg-red-100">Bloquer</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-on-surface-variant font-ui-small text-ui-small text-center py-4">Aucun utilisateur signalé.</p>
                @endforelse
                
                <a href="{{ route('admin.moderation') }}" class="block text-center mt-4 font-ui-small text-ui-small text-community-indigo border border-community-indigo/30 rounded py-2 hover:bg-community-indigo/5 transition-colors">Gérer les utilisateurs</a>
            </div>
            
            <!-- Quick Actions -->
            <div class="mt-8 pt-6 border-t border-surface-border dark:border-surface-tint space-y-3">
                <a href="{{ route('admin.categories') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-community-indigo font-ui-small transition-colors">
                    <span class="material-symbols-outlined text-[18px]">category</span> Gérer les catégories
                </a>
                <a href="{{ route('blog.create') }}" class="flex items-center gap-2 text-on-surface-variant hover:text-community-indigo font-ui-small transition-colors">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Nouvel article officiel
                </a>
            </div>
        </section>
    </div>
</main>
@endsection
