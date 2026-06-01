@extends('layouts.app')

@section('content')
<div class="max-w-container-max mx-auto space-y-8">
    <div class="flex items-center gap-3 border-b border-surface-border dark:border-surface-tint pb-4 mb-8">
        <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-official-ink dark:text-paper-white">Modération Complète</h1>
        <span class="font-ui-medium text-community-indigo border-l-2 border-surface-border pl-3">Admin Workspace</span>
    </div>

    <!-- File d'attente complète -->
    <section class="bg-surface-container-lowest dark:bg-primary-container/10 border border-surface-border dark:border-surface-tint rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="font-ui-medium text-ui-medium font-bold text-official-ink dark:text-paper-white flex items-center gap-2">
                <span class="material-symbols-outlined text-reaction-red">flag</span>
                File d'attente des signalements
            </h2>
            <span class="bg-surface-container dark:bg-primary-container px-3 py-1 rounded-full font-label-caps text-label-caps text-on-surface-variant">Total: {{ count($flaggedItems) }}</span>
        </div>
        
        <div class="space-y-4">
            @forelse($flaggedItems as $item)
                @php($content = $item['reportable'])
                <div class="border border-surface-border dark:border-surface-tint rounded p-4">
                    <div class="flex flex-col md:flex-row justify-between items-start mb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-ui-small text-ui-small font-bold text-official-ink dark:text-paper-white">{{ class_basename($content) }} #{{ $content->id }}</span>
                            <span class="text-surface-variant hidden md:inline">•</span>
                            <span class="font-ui-small text-ui-small text-official-ink dark:text-paper-white">Auteur: {{ '@' . $content->user->username }}</span>
                            <span class="text-surface-variant hidden md:inline">•</span>
                            <div class="flex gap-1 flex-wrap">
                                @foreach($item['reports'] as $report)
                                    <span class="font-ui-small text-ui-small text-reaction-red bg-error-container dark:bg-on-error-container/20 px-2 py-0.5 rounded text-xs" title="Signalé par: {{ $report->user->name }}">
                                        {{ $report->reason_label }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        <span class="font-ui-small text-ui-small text-on-surface-variant whitespace-nowrap">{{ $item['reports_count'] }} signalements</span>
                    </div>
                    
                    <p class="font-article-body text-article-body text-on-surface dark:text-surface-container-highest text-base leading-relaxed mb-4 pl-4 border-l-2 border-surface-variant dark:border-surface-tint">
                        "{{ \Illuminate\Support\Str::limit(strip_tags($content->body ?? $content->title ?? 'Contenu'), 300) }}"
                    </p>
                    
                    <div class="flex gap-3 flex-wrap border-t border-surface-border dark:border-surface-tint pt-3">
                        <a href="{{ $content instanceof \App\Models\Post ? ($content->isBlog() ? route('blog.show', $content) : route('community.show', $content)) : route('community.show', $content->post) }}" target="_blank" class="font-ui-small text-ui-small border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white px-3 py-1.5 rounded hover:bg-surface-container dark:hover:bg-primary-container transition-colors">
                            Voir le contexte
                        </a>
                        @if($item['reports']->isNotEmpty())
                            <form method="POST" action="{{ route('admin.reports.ignore', $item['reports']->first()) }}">
                                @csrf
                                <button class="font-ui-small text-ui-small border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white px-3 py-1.5 rounded hover:bg-surface-container dark:hover:bg-primary-container transition-colors">Ignorer (Tout)</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.content.destroy', ['type' => $content instanceof \App\Models\Post ? 'post' : 'comment', 'id' => $content->id]) }}">
                            @csrf @method('DELETE')
                            <button class="font-ui-small text-ui-small bg-reaction-red text-white px-3 py-1.5 rounded hover:bg-red-600 transition-colors">Supprimer Contenu</button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.block', $content->user) }}">
                            @csrf
                            <button class="font-ui-small text-ui-small border border-reaction-red text-reaction-red px-3 py-1.5 rounded hover:bg-error-container dark:hover:bg-on-error-container/20 transition-colors">Bloquer Auteur</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-on-surface-variant font-ui-small text-ui-small text-center py-8">Aucun signalement en attente dans la file.</p>
            @endforelse
        </div>
    </section>

    <!-- Liste des Utilisateurs -->
    <section class="bg-surface-container-lowest dark:bg-primary-container/10 border border-surface-border dark:border-surface-tint rounded-lg shadow-[0px_4px_20px_rgba(15,23,42,0.05)] p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-ui-medium text-ui-medium font-bold text-official-ink dark:text-paper-white flex items-center gap-2">
                <span class="material-symbols-outlined text-community-indigo">group</span>
                Gestion des Utilisateurs
            </h2>
            <span class="bg-surface-container dark:bg-primary-container px-3 py-1 rounded-full font-label-caps text-label-caps text-on-surface-variant">Total: {{ $users->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="text-on-surface-variant font-ui-small border-b border-surface-border dark:border-surface-tint">
                    <tr>
                        <th class="py-3 pr-4 font-semibold">Utilisateur</th>
                        <th class="py-3 pr-4 font-semibold">Email</th>
                        <th class="py-3 pr-4 font-semibold">Rôle</th>
                        <th class="py-3 pr-4 font-semibold">Statut</th>
                        <th class="py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-border dark:divide-surface-tint">
                    @foreach($users as $user)
                        <tr class="hover:bg-surface-container dark:hover:bg-primary-container/20 transition-colors">
                            <td class="py-3 pr-4">
                                <div class="font-ui-medium font-medium text-official-ink dark:text-paper-white">{{ $user->name }}</div>
                                <div class="font-ui-small text-xs text-on-surface-variant">@{{ $user->username }}</div>
                            </td>
                            <td class="py-3 pr-4 font-ui-small text-on-surface-variant">{{ $user->email }}</td>
                            <td class="py-3 pr-4 font-ui-small text-on-surface-variant">{{ $user->is_admin ? 'Admin' : 'Membre' }}</td>
                            <td class="py-3 pr-4 font-ui-small">
                                <span class="{{ $user->is_blocked ? 'text-reaction-red' : 'text-community-teal' }}">
                                    {{ $user->is_blocked ? 'Bloqué' : 'Actif' }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    @if($user->is_blocked)
                                        <form method="POST" action="{{ route('admin.users.unblock', $user) }}">
                                            @csrf
                                            <button class="font-ui-small text-xs border border-surface-border dark:border-surface-tint px-3 py-1.5 rounded text-on-surface-variant hover:bg-surface-container dark:hover:bg-primary-container transition-colors">Débloquer</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.block', $user) }}">
                                            @csrf
                                            <button class="font-ui-small text-xs bg-error-container dark:bg-on-error-container/20 text-reaction-red px-3 py-1.5 rounded hover:bg-red-100 transition-colors">Bloquer</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </section>
</div>
@endsection
