<?php

namespace App\Http\Controllers;

use App\Events\CommentPosted;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function store(StoreCommentRequest $request, Post $post): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Comment::class);

        $parentId = $request->validated('parent_id');

        $parent = null;

        if ($parentId) {
            $parent = Comment::query()->with('user')->findOrFail($parentId);

            if ($parent->post_id !== $post->id) {
                abort(422, 'Le commentaire parent n\'appartient pas à cette publication.');
            }
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'parent_id' => $parentId,
            'body' => $request->validated('body'),
        ]);

        $comment->load('user');

        if ($parent) {
            $comment->setRelation('post', $post);
            $this->notifications->notifyReply($parent, $comment);
        } else {
            $this->notifications->notifyComment($post, $comment);
        }

        $this->notifications->notifyMentions($comment->body, $comment, $post);

        CommentPosted::dispatch($comment);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
            ]);
        }

        return back()->with('success', 'Commentaire publié.');
    }

    public function update(StoreCommentRequest $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $comment);

        $comment->update(['body' => $request->validated('body')]);

        $this->notifications->notifyMentions($comment->body, $comment, $comment->post);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment->fresh(),
            ]);
        }

        return back()->with('success', 'Commentaire modifié.');
    }

    public function destroy(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Commentaire supprimé.');
    }
}
