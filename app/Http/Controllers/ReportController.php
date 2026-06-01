<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Report::class);

        $reportable = $this->resolveReportable(
            $request->validated('reportable_type'),
            (int) $request->validated('reportable_id')
        );

        $exists = Report::query()
            ->where('user_id', $request->user()->id)
            ->where('reportable_id', $reportable->id)
            ->where('reportable_type', $reportable->getMorphClass())
            ->exists();

        if ($exists) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà signalé ce contenu.',
                ], 409);
            }

            return back()->with('error', 'Vous avez déjà signalé ce contenu.');
        }

        Report::create([
            'user_id' => $request->user()->id,
            'reportable_id' => $reportable->id,
            'reportable_type' => $reportable->getMorphClass(),
            'reason' => $request->validated('reason'),
            'status' => Report::STATUS_PENDING,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Signalement envoyé. Merci pour votre vigilance.',
            ]);
        }

        return back()->with('success', 'Signalement envoyé. Merci pour votre vigilance.');
    }

    private function resolveReportable(string $type, int $id): Model
    {
        return match ($type) {
            'post' => Post::findOrFail($id),
            'comment' => Comment::findOrFail($id),
            default => abort(422),
        };
    }
}
