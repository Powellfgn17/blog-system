@php
    $d = $depth ?? 0;
    $reactIcons = \App\Models\Reaction::ICONS;
    $canEdit   = auth()->check() && (auth()->id() === $comment->user_id || auth()->user()?->is_admin);
    $canDelete = auth()->check() && (auth()->id() === $comment->user_id || auth()->user()?->is_admin);
    $canReport = auth()->check() && auth()->id() !== $comment->user_id;

    // Render @username as clickable links (CDC §6.5)
    $renderedBody = preg_replace_callback(
        '/@([a-zA-Z0-9_]{3,30})/',
        fn($m) => '<a href="'.route('profile.show', $m[1]).'" class="text-community-indigo dark:text-secondary-fixed hover:underline font-medium">@'.$m[1].'</a>',
        e($comment->body)
    );
@endphp

<div
    class="{{ $d === 0 ? 'flex gap-4' : 'mt-4 flex gap-4 pl-6 relative' }}"
    data-comment-id="{{ $comment->id }}"
>
    @if ($d !== 0)
        <div class="absolute left-[-12px] top-0 bottom-0 w-[2px] bg-surface-border dark:bg-surface-tint"></div>
    @endif

    <img
        alt="{{ $comment->user->name }} avatar"
        class="{{ $d === 0 ? 'w-10 h-10' : 'w-8 h-8' }} rounded-full object-cover flex-shrink-0"
        src="{{ $comment->user->avatar_url }}"
    >

    <div class="flex-grow min-w-0">
        <div class="bg-paper-white dark:bg-official-ink p-4 rounded-lg border border-surface-border dark:border-surface-tint shadow-[0_4px_20px_rgba(15,23,42,0.05)]">

            {{-- Header --}}
            <div class="flex justify-between items-start mb-2 gap-2">
                <div class="flex items-baseline gap-2 flex-wrap">
                    <a href="{{ route('profile.show', $comment->user->username) }}" class="font-ui-medium text-ui-medium font-semibold text-official-ink dark:text-paper-white hover:text-community-indigo dark:hover:text-secondary-fixed transition-colors">
                        {{ $comment->user->name }}
                    </a>
                    <span class="font-ui-small text-ui-small text-on-surface-variant">{{ $comment->created_at->diffForHumans() }}</span>
                    @if ($comment->wasEdited())
                        <span class="font-ui-small text-ui-small text-on-surface-variant italic">· modifié</span>
                    @endif
                </div>

                {{-- Inline actions menu --}}
                @auth
                    <div class="flex items-center gap-1 flex-shrink-0">
                        {{-- Edit (CDC §6.3) --}}
                        @if($canEdit)
                            <button
                                type="button"
                                onclick="toggleEditForm({{ $comment->id }})"
                                class="p-1 rounded hover:bg-surface-container dark:hover:bg-primary-container text-on-surface-variant hover:text-community-indigo dark:hover:text-secondary-fixed transition-colors"
                                title="Modifier"
                            >
                                <span translate="no" class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                        @endif

                        {{-- Delete (CDC §6.3) --}}
                        @if($canDelete)
                            <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('Supprimer ce commentaire ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 rounded hover:bg-error-container dark:hover:bg-on-error-container/10 text-on-surface-variant hover:text-on-error-container dark:hover:text-reaction-red transition-colors" title="Supprimer">
                                    <span translate="no" class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        @endif

                        {{-- Report (CDC §7.1) --}}
                        @if($canReport)
                            <button
                                type="button"
                                onclick="openReportModal('comment', {{ $comment->id }})"
                                class="p-1 rounded hover:bg-surface-container dark:hover:bg-primary-container text-on-surface-variant hover:text-on-surface transition-colors"
                                title="Signaler"
                            >
                                <span translate="no" class="material-symbols-outlined text-[18px]">flag</span>
                            </button>
                        @endif
                    </div>
                @endauth
            </div>

            {{-- Body --}}
            <div id="comment-body-{{ $comment->id }}" class="font-ui-small text-ui-small text-official-ink dark:text-surface-container-highest leading-relaxed">
                {!! nl2br($renderedBody) !!}
            </div>

            {{-- Edit form (hidden by default) --}}
            @if($canEdit)
                <form
                    id="edit-form-{{ $comment->id }}"
                    method="POST"
                    action="{{ route('comments.update', $comment) }}"
                    class="hidden mt-3"
                >
                    @csrf @method('PUT')
                    <textarea
                        name="body"
                        rows="3"
                        class="w-full bg-surface-container-low dark:bg-primary-container border border-surface-border dark:border-surface-tint rounded-lg p-3 font-ui-small text-ui-small focus:outline-none focus:border-community-teal focus:ring-1 focus:ring-community-teal resize-none text-official-ink dark:text-paper-white"
                    >{{ $comment->body }}</textarea>
                    <div class="flex gap-2 mt-2 justify-end">
                        <button type="button" onclick="toggleEditForm({{ $comment->id }})" class="px-4 py-1 rounded-full border border-surface-border font-ui-small text-ui-small text-on-surface-variant hover:bg-surface-container transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="px-4 py-1 bg-community-indigo dark:bg-secondary-fixed text-white dark:text-official-ink rounded-full font-ui-small text-ui-small hover:opacity-90 transition-opacity">
                            Enregistrer
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Reactions + Reply --}}
        @auth
            <div class="mt-2 flex flex-wrap gap-2 items-center">
                {{-- Reactions (CDC §6.4) --}}
                <div data-reaction-container class="flex flex-wrap gap-1">
                    @foreach ($reactIcons as $type => $icon)
                        @php
                            $count = $comment->reactions->where('type', $type)->count();
                            $hasReacted = auth()->check() ? $comment->reactions->where('user_id', auth()->id())->where('type', $type)->isNotEmpty() : false;
                        @endphp
                        <button
                            type="button"
                            data-reactable-type="comment"
                            data-reactable-id="{{ $comment->id }}"
                            data-reaction-type="{{ $type }}"
                            class="inline-flex items-center gap-1 px-2 py-1 rounded-full transition-colors border border-surface-border dark:border-transparent group {{ $hasReacted ? 'bg-community-indigo/10 text-community-indigo' : 'bg-surface-container dark:bg-surface-container-low hover:bg-surface-dim dark:hover:bg-primary-container' }}"
                        >
                            <span translate="no" class="material-symbols-outlined text-[16px] {{ $hasReacted ? 'text-community-indigo' : 'group-hover:text-community-indigo' }}" data-icon="{{ $icon }}" style="{{ $hasReacted ? 'font-variation-settings: \'FILL\' 1;' : '' }}">{{ $icon }}</span>
                            <span class="font-ui-small text-[11px] font-medium {{ $hasReacted ? 'text-community-indigo' : 'text-on-surface-variant group-hover:text-community-indigo' }}" data-reaction-count="{{ $type }}">{{ $count }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Reply button --}}
                <button
                    class="inline-flex items-center gap-1 px-3 py-1 bg-surface-container dark:bg-surface-container-low hover:bg-surface-dim dark:hover:bg-primary-container transition-colors rounded-full border border-surface-border dark:border-transparent text-on-surface-variant group"
                    onclick="this.nextElementSibling.classList.toggle('hidden')"
                >
                    <span translate="no" class="material-symbols-outlined text-[16px] group-hover:text-official-ink dark:group-hover:text-paper-white">chat_bubble_outline</span>
                    <span class="font-ui-small text-ui-small font-medium group-hover:text-official-ink dark:group-hover:text-paper-white">Répondre</span>
                </button>

                {{-- Reply form --}}
                <form method="POST" action="{{ route('comments.store', $post) }}" class="hidden w-full mt-2 flex gap-2">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea
                        data-mentions
                        name="body"
                        rows="2"
                        class="flex-grow bg-paper-white dark:bg-official-ink border border-surface-border dark:border-surface-tint rounded-lg p-3 font-ui-small text-ui-small focus:outline-none focus:border-community-teal focus:ring-1 focus:ring-community-teal resize-none text-official-ink dark:text-paper-white"
                        placeholder="Répondre… Tapez @ pour mentionner"
                    ></textarea>
                    <button class="bg-official-ink dark:bg-paper-white text-paper-white dark:text-official-ink px-4 py-2 rounded-lg font-ui-medium text-ui-medium hover:opacity-90 transition-opacity self-end" type="submit">
                        Envoyer
                    </button>
                </form>
            </div>
        @endauth

        {{-- Nested replies --}}
        <div data-comment-replies class="mt-2 space-y-4">
            @foreach ($comment->replies as $reply)
                @include('comments._comment', ['comment' => $reply, 'post' => $post, 'depth' => $d + 1])
            @endforeach
        </div>
    </div>
</div>
