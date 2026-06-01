function getCsrfToken() {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}

function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function showToast(message, kind = 'error') {
  const containerId = 'js-toast-container';
  let container = document.getElementById(containerId);

  if (!container) {
    container = document.createElement('div');
    container.id = containerId;
    container.className = 'fixed right-4 top-4 z-[100] flex flex-col gap-2';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  const kindClass =
    kind === 'success'
      ? 'bg-community-teal text-white'
      : 'bg-red-600 text-white';

  toast.className = `rounded-md px-4 py-3 text-sm shadow-lg ${kindClass}`;
  toast.textContent = message;
  container.appendChild(toast);

  window.setTimeout(() => {
    toast.remove();
  }, 2800);
}

async function jsonFetch(url, options = {}) {
  const res = await fetch(url, {
    credentials: 'same-origin',
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': getCsrfToken(),
      Accept: 'application/json',
      ...(options.headers || {}),
    },
    ...options,
  });

  const isJson = (res.headers.get('content-type') || '').includes('application/json');
  const data = isJson ? await res.json() : null;

  if (!res.ok) {
    const message = data?.message || `Erreur ${res.status}`;
    throw new Error(message);
  }

  return data;
}

function setReactionCounts(container, counts) {
  if (!counts) return;
  for (const [type, total] of Object.entries(counts)) {
    const badge = container.querySelector(`[data-reaction-count="${type}"]`);
    if (badge) badge.textContent = String(total);
  }
}

function setActiveReaction(container, userReaction) {
  container.querySelectorAll('[data-reaction-type]').forEach((btn) => {
    const type = btn.getAttribute('data-reaction-type');
    const active = userReaction && type === userReaction;
    btn.classList.toggle('ring-2', !!active);
    btn.classList.toggle('ring-community-indigo', !!active);
    btn.classList.toggle('bg-community-indigo/10', !!active);
  });
}

async function handleBookmarkClick(e) {
  const btn = e.target.closest('[data-bookmark-post-id]');
  if (!btn) return;

  e.preventDefault();

  const postId = btn.getAttribute('data-bookmark-post-id');
  btn.disabled = true;
  btn.classList.add('opacity-70');

  try {
    const data = await jsonFetch(`/bookmarks/${postId}`, { method: 'POST' });
    btn.textContent = data.bookmarked ? 'Retirer des favoris' : 'Ajouter aux favoris';
    btn.classList.toggle('bg-surface-container', !data.bookmarked);
    btn.classList.toggle('bg-community-indigo', data.bookmarked);
    btn.classList.toggle('text-white', data.bookmarked);
  } catch (err) {
    showToast(err.message);
  } finally {
    btn.disabled = false;
    btn.classList.remove('opacity-70');
  }
}

async function handleReactionClick(e) {
  const btn = e.target.closest('[data-reactable-type][data-reactable-id][data-reaction-type]');
  if (!btn) return;

  e.preventDefault();

  const reactableType = btn.getAttribute('data-reactable-type');
  const reactableId = btn.getAttribute('data-reactable-id');
  const type = btn.getAttribute('data-reaction-type');
  const container = btn.closest('[data-reaction-container]');

  btn.disabled = true;
  btn.classList.add('opacity-70');

  try {
    const data = await jsonFetch('/reactions', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        reactable_type: reactableType,
        reactable_id: Number(reactableId),
        type,
      }),
    });

    if (container) {
      setReactionCounts(container, data.counts);
      setActiveReaction(container, data.user_reaction);
    }
  } catch (err) {
    showToast(err.message);
  } finally {
    btn.disabled = false;
    btn.classList.remove('opacity-70');
  }
}

async function handleMarkNotificationRead(e) {
  const btn = e.target.closest('[data-notification-read-id]');
  if (!btn) return;
  e.preventDefault();

  const id = btn.getAttribute('data-notification-read-id');
  btn.disabled = true;

  try {
    const data = await jsonFetch(`/notifications/${id}/read`, { method: 'POST' });
    if (data?.url) window.location.href = data.url;
  } catch (err) {
    showToast(err.message);
  } finally {
    btn.disabled = false;
  }
}

async function handleReadAllNotifications(e) {
  const btn = e.target.closest('[data-notifications-read-all]');
  if (!btn) return;
  e.preventDefault();
  btn.disabled = true;

  try {
    await jsonFetch('/notifications/read-all', { method: 'POST' });
    window.location.reload();
  } catch (err) {
    showToast(err.message);
  } finally {
    btn.disabled = false;
  }
}

function formatRelativeTime(iso) {
  if (!iso) return 'à l’instant';
  const diffSec = Math.max(0, Math.floor((Date.now() - new Date(iso).getTime()) / 1000));
  if (diffSec < 60) return 'à l’instant';
  if (diffSec < 3600) return `il y a ${Math.floor(diffSec / 60)} min`;
  if (diffSec < 86400) return `il y a ${Math.floor(diffSec / 3600)} h`;
  return `il y a ${Math.floor(diffSec / 86400)} j`;
}

function commentCardTemplate(comment, isReply = false) {
  const wrapperClass = isReply ? 'mt-4 flex gap-4 pl-6 relative' : 'flex gap-4';
  const border = isReply ? '<div class="absolute left-[-12px] top-0 bottom-0 w-[2px] bg-surface-border"></div>' : '';
  const avatarUrl = escapeHtml(comment?.user?.avatar_url || '');
  const userName = escapeHtml(comment?.user?.name || 'Utilisateur');
  const body = escapeHtml(comment?.body || '');
  const createdAt = formatRelativeTime(comment?.created_at);
  const id = Number(comment?.id || 0);

  return `<div class="${wrapperClass}" data-comment-id="${id}">
    ${border}
    <img alt="${userName} avatar" class="w-10 h-10 rounded-full object-cover" src="${avatarUrl}">
    <div class="flex-grow">
      <div class="bg-paper-white p-4 rounded-lg border border-surface-border shadow-[0_4px_20px_rgba(15,23,42,0.05)]">
        <div class="flex justify-between items-start mb-2">
          <div>
            <span class="font-ui-medium text-ui-medium font-semibold">${userName}</span>
            <span class="font-ui-small text-ui-small text-on-surface-variant ml-2">${createdAt}</span>
          </div>
        </div>
        <p class="font-ui-small text-ui-small text-official-ink leading-relaxed">${body}</p>
        <div data-comment-replies></div>
      </div>
    </div>
  </div>`;
}

function insertRealtimeComment(section, payload) {
  const comment = payload?.comment;
  const commentId = Number(comment?.id || 0);
  if (!commentId) return;

  if (section.querySelector(`[data-comment-id="${commentId}"]`)) return;

  const root = section.querySelector('[data-comments-root]');
  if (!root) return;

  const emptyState = section.querySelector('[data-comments-empty]');
  if (emptyState) emptyState.remove();

  const wrapper = document.createElement('div');
  wrapper.innerHTML = commentCardTemplate(comment, Boolean(comment.parent_id));
  const node = wrapper.firstElementChild;
  if (!node) return;

  if (comment.parent_id) {
    const parent = section.querySelector(`[data-comment-id="${Number(comment.parent_id)}"]`);
    const replies = parent?.querySelector('[data-comment-replies]');
    if (replies) {
      replies.appendChild(node);
      return;
    }
  }

  root.prepend(node);
}

function initRealtimeComments() {
  const section = document.querySelector('[data-realtime-comments][data-post-id]');
  const postId = section?.getAttribute('data-post-id');

  if (!section || !postId || !window.Echo) return;

  window.Echo.channel(`post.${postId}`).listen('.comment.posted', (payload) => {
    insertRealtimeComment(section, payload);
  });
}

function buildMentionList(container, users, onPick) {
  container.innerHTML = '';
  container.classList.remove('hidden');

  users.forEach((u) => {
    const item = document.createElement('button');
    item.type = 'button';
    item.className =
      'w-full text-left px-3 py-2 hover:bg-surface-container flex items-center gap-2';
    item.innerHTML = `<img src="${u.avatar_url}" class="w-6 h-6 rounded-full" alt="" /><span class="text-sm font-medium">@${u.username}</span><span class="text-xs text-on-surface-variant">${u.name}</span>`;
    item.addEventListener('click', () => onPick(u));
    container.appendChild(item);
  });
}

function initMentions(textarea) {
  const wrapper = document.createElement('div');
  wrapper.className = 'relative';
  textarea.parentNode.insertBefore(wrapper, textarea);
  wrapper.appendChild(textarea);

  const dropdown = document.createElement('div');
  dropdown.className =
    'hidden absolute z-50 mt-2 w-full bg-paper-white border border-surface-border rounded-lg overflow-hidden shadow-[0px_4px_20px_rgba(15,23,42,0.05)]';
  wrapper.appendChild(dropdown);

  let lastQuery = '';
  let lastAtIndex = -1;

  textarea.addEventListener('input', async () => {
    const value = textarea.value;
    const caret = textarea.selectionStart || 0;
    const upto = value.slice(0, caret);
    const at = upto.lastIndexOf('@');

    if (at === -1) {
      dropdown.classList.add('hidden');
      return;
    }

    const query = upto.slice(at + 1);
    if (!/^[a-zA-Z0-9_]{0,30}$/.test(query)) {
      dropdown.classList.add('hidden');
      return;
    }

    if (query.length < 1) {
      dropdown.classList.add('hidden');
      return;
    }

    if (query === lastQuery && at === lastAtIndex) return;
    lastQuery = query;
    lastAtIndex = at;

    try {
      const data = await jsonFetch(`/users/search?q=${encodeURIComponent(query)}`, {
        method: 'GET',
      });

      const users = data.users || [];
      if (users.length === 0) {
        dropdown.classList.add('hidden');
        return;
      }

      buildMentionList(dropdown, users, (u) => {
        const before = value.slice(0, at);
        const after = value.slice(caret);
        const insert = `@${u.username} `;
        textarea.value = before + insert + after;
        textarea.focus();
        dropdown.classList.add('hidden');
      });
    } catch {
      dropdown.classList.add('hidden');
    }
  });

  document.addEventListener('click', (e) => {
    if (!wrapper.contains(e.target)) dropdown.classList.add('hidden');
  });
}

document.addEventListener('click', (e) => {
  handleBookmarkClick(e);
  handleReactionClick(e);
  handleReadAllNotifications(e);
  handleMarkNotificationRead(e);
});

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('textarea[data-mentions]').forEach((ta) => initMentions(ta));
  initRealtimeComments();
});

