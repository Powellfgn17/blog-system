<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Comment $comment) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('post.'.$this->comment->post_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'comment.posted';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $this->comment->load('user');

        return [
            'comment' => [
                'id' => $this->comment->id,
                'body' => $this->comment->body,
                'parent_id' => $this->comment->parent_id,
                'created_at' => $this->comment->created_at->toIso8601String(),
                'user' => [
                    'id' => $this->comment->user->id,
                    'name' => $this->comment->user->name,
                    'username' => $this->comment->user->username,
                    'avatar_url' => $this->comment->user->avatar_url,
                ],
            ],
        ];
    }
}
