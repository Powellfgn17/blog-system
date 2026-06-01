@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-0 md:px-margin-desktop py-4 flex flex-col gap-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b border-surface-border pb-6">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-4xl text-official-ink dark:text-paper-white" style="font-variation-settings: 'FILL' 1;">notifications</span>
            <div>
                <h1 class="font-display-xl-mobile md:font-display-xl font-extrabold text-official-ink dark:text-paper-white tracking-tight">Notifications</h1>
                <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim mt-2">Restez au courant de l'activité sur vos publications.</p>
            </div>
        </div>
        @if($notifications->whereNull('read_at')->count() > 0)
            <button id="mark-all-read" class="flex items-center gap-2 px-6 py-2 rounded-full border border-surface-border dark:border-surface-tint text-official-ink dark:text-paper-white hover:bg-surface-container dark:hover:bg-primary-container transition-colors font-ui-medium text-ui-medium">
                <span class="material-symbols-outlined text-[18px]">done_all</span> Tout marquer comme lu
            </button>
        @endif
    </div>

    <div class="flex flex-col gap-4">
        @forelse($notifications as $notification)
            @php
                $isRead = $notification->read_at !== null;
                $icon = match($notification->type) {
                    'comment' => 'chat_bubble',
                    'reply' => 'reply',
                    'reaction' => 'favorite',
                    'mention' => 'alternate_email',
                    default => 'notifications'
                };
                $iconColor = match($notification->type) {
                    'reaction' => 'text-reaction-red',
                    'comment', 'reply' => 'text-community-teal',
                    'mention' => 'text-community-indigo',
                    default => 'text-on-surface-variant'
                };
            @endphp
            <article class="notification-item flex gap-4 p-5 rounded-xl border transition-all duration-300 {{ $isRead ? 'bg-paper-white dark:bg-official-ink border-surface-border dark:border-surface-tint opacity-80' : 'bg-surface-container-low dark:bg-primary-container/30 border-community-indigo/30 dark:border-secondary-fixed/30 shadow-[0px_4px_20px_rgba(79,70,229,0.05)] cursor-pointer hover:shadow-[0px_6px_24px_rgba(79,70,229,0.08)]' }}" data-id="{{ $notification->id }}" data-url="{{ $notification->data['url'] ?? '#' }}" data-read="{{ $isRead ? 'true' : 'false' }}">
                <div class="w-12 h-12 rounded-full bg-surface-container dark:bg-surface-container-low flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined {{ $iconColor }}">{{ $icon }}</span>
                </div>
                <div class="flex-grow flex flex-col justify-center">
                    <p class="font-ui-medium text-ui-medium text-official-ink dark:text-paper-white leading-snug">
                        {!! str_replace(['<b>', '</b>'], ['<span class="font-bold text-community-indigo dark:text-secondary-fixed">', '</span>'], e($notification->data['message'] ?? 'Nouvelle notification')) !!}
                    </p>
                    <span class="font-ui-small text-xs text-on-surface-variant dark:text-surface-dim mt-1">{{ $notification->created_at->diffForHumans() }}</span>
                </div>
                @if(!$isRead)
                    <div class="w-3 h-3 bg-community-indigo dark:bg-secondary-fixed rounded-full self-center flex-shrink-0 mr-2"></div>
                @endif
            </article>
        @empty
            <div class="py-16 flex flex-col items-center justify-center text-center bg-surface-container-low dark:bg-primary-container/20 rounded-2xl border border-dashed border-surface-border dark:border-surface-tint">
                <span class="material-symbols-outlined text-[64px] text-surface-tint dark:text-on-surface-variant mb-4">notifications_off</span>
                <h3 class="font-headline-lg-mobile text-2xl font-bold text-official-ink dark:text-paper-white mb-2">Aucune notification</h3>
                <p class="font-ui-medium text-ui-medium text-on-surface-variant dark:text-surface-dim max-w-md">Vous n'avez aucune notification pour le moment. L'activité de la communauté apparaîtra ici.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-4 flex justify-center w-full">
            {{ $notifications->links() }}
        </div>
    @endif
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Marquer une notification comme lue au clic et rediriger
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', async () => {
            const url = item.dataset.url;
            const id = item.dataset.id;
            const isRead = item.dataset.read === 'true';

            if (!isRead) {
                try {
                    await fetch(`/notifications/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });
                } catch (e) {
                    console.error(e);
                }
            }
            if (url && url !== '#') {
                window.location.href = url;
            }
        });
    });

    // Tout marquer comme lu
    const markAllBtn = document.getElementById('mark-all-read');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async () => {
            try {
                const res = await fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (res.ok) {
                    window.location.reload();
                }
            } catch (e) {
                console.error(e);
            }
        });
    }
});
</script>
@endsection
