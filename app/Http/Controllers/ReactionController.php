<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReactionController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reactable_type' => ['required', 'string', Rule::in(['post', 'comment'])],
            'reactable_id' => ['required', 'integer'],
            'type' => ['required', 'string', Rule::in(Reaction::TYPES)],
        ]);

        $reactable = $this->resolveReactable($validated['reactable_type'], (int) $validated['reactable_id']);

        $existing = Reaction::query()
            ->where('user_id', $request->user()->id)
            ->where('reactable_id', $reactable->id)
            ->where('reactable_type', $reactable->getMorphClass())
            ->first();

        if ($existing && $existing->type === $validated['type']) {
            $existing->delete();
        } elseif ($existing) {
            $existing->update(['type' => $validated['type']]);
            $this->notifyReactionAuthor($reactable, $existing);
        } else {
            $reaction = Reaction::create([
                'user_id' => $request->user()->id,
                'reactable_id' => $reactable->id,
                'reactable_type' => $reactable->getMorphClass(),
                'type' => $validated['type'],
            ]);
            $this->notifyReactionAuthor($reactable, $reaction);
        }

        return response()->json([
            'success' => true,
            'counts' => $this->reactionCounts($reactable),
            'user_reaction' => Reaction::query()
                ->where('user_id', $request->user()->id)
                ->where('reactable_id', $reactable->id)
                ->where('reactable_type', $reactable->getMorphClass())
                ->value('type'),
        ]);
    }

    /**
     * @return array<string, int>
     */
    private function reactionCounts(Model $reactable): array
    {
        $counts = [];

        foreach (Reaction::TYPES as $type) {
            $counts[$type] = 0;
        }

        $reactable->reactions()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->each(function ($total, $type) use (&$counts) {
                $counts[$type] = (int) $total;
            });

        return $counts;
    }

    private function resolveReactable(string $type, int $id): Model
    {
        return match ($type) {
            'post' => Post::findOrFail($id),
            'comment' => Comment::findOrFail($id),
            default => abort(422),
        };
    }

    private function notifyReactionAuthor(Model $reactable, Reaction $reaction): void
    {
        $author = $reactable instanceof Post
            ? $reactable->user
            : $reactable->user;

        $this->notifications->notifyReaction($author, $reaction, $reactable);
    }
}
