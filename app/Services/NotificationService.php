<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public function notify(User $recipient, string $type, Model $notifiable, array $data = []): void
    {
        if ($recipient->id === auth()->id()) {
            return;
        }

        Notification::create([
            'user_id' => $recipient->id,
            'type' => $type,
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => $notifiable->getMorphClass(),
            'data' => $data,
        ]);
    }

    public function notifyComment(Post $post, Comment $comment): void
    {
        $this->notify($post->user, Notification::TYPE_COMMENT, $comment, [
            'message' => auth()->user()->name.' a commenté votre publication.',
            'post_id' => $post->id,
            'url' => $this->postUrl($post),
        ]);
    }

    public function notifyReply(Comment $parent, Comment $reply): void
    {
        $post = $reply->relationLoaded('post') ? $reply->post : $reply->post()->first();

        $this->notify($parent->user, Notification::TYPE_REPLY, $reply, [
            'message' => auth()->user()->name.' a répondu à votre commentaire.',
            'post_id' => $reply->post_id,
            'url' => $post ? $this->postUrl($post) : '#',
        ]);
    }

    public function notifyReaction(User $author, Reaction $reaction, Model $reactable): void
    {
        $postId = $reactable instanceof Post ? $reactable->id : $reactable->post_id;

        $this->notify($author, Notification::TYPE_REACTION, $reaction, [
            'message' => auth()->user()->name.' a réagi à votre contenu.',
            'post_id' => $postId,
            'url' => $this->postUrl($reactable instanceof Post ? $reactable : $reactable->post),
        ]);
    }

    public function notifyMentions(string $body, Model $notifiable, Post $post): void
    {
        preg_match_all('/@([a-zA-Z0-9_]{3,30})/', $body, $matches);

        $usernames = array_unique($matches[1] ?? []);

        if ($usernames === []) {
            return;
        }

        $users = User::query()->whereIn('username', $usernames)->get();

        foreach ($users as $user) {
            $this->notify($user, Notification::TYPE_MENTION, $notifiable, [
                'message' => auth()->user()->name.' vous a mentionné.',
                'post_id' => $post->id,
                'url' => $this->postUrl($post),
            ]);
        }
    }

    private function postUrl(Post $post): string
    {
        return $post->isBlog()
            ? route('blog.show', $post)
            : route('community.show', $post);
    }
}
