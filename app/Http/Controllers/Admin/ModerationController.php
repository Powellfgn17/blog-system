<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(): View
    {
        $flagged = Report::query()
            ->pending()
            ->select('reportable_type', 'reportable_id', DB::raw('COUNT(*) as reports_count'))
            ->groupBy('reportable_type', 'reportable_id')
            ->orderByDesc('reports_count')
            ->get()
            ->map(function ($row) {
                $model = $this->resolveReportable($row->reportable_type, (int) $row->reportable_id);

                return [
                    'reportable' => $model,
                    'reports_count' => (int) $row->reports_count,
                    'reports' => Report::query()
                        ->pending()
                        ->where('reportable_type', $row->reportable_type)
                        ->where('reportable_id', $row->reportable_id)
                        ->with('user')
                        ->get(),
                ];
            })
            ->filter(fn ($item) => $item['reportable'] !== null);

        return view('admin.moderation', ['flaggedItems' => $flagged]);
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $content = $this->resolveReportableByType($type, $id);
        $content->delete();

        Report::query()
            ->where('reportable_type', $content->getMorphClass())
            ->where('reportable_id', $content->id)
            ->update(['status' => Report::STATUS_RESOLVED]);

        return back()->with('success', 'Contenu supprimé.');
    }

    public function ignore(Report $report): RedirectResponse
    {
        Report::query()
            ->where('reportable_type', $report->reportable_type)
            ->where('reportable_id', $report->reportable_id)
            ->pending()
            ->update(['status' => Report::STATUS_IGNORED]);

        return back()->with('success', 'Signalements ignorés.');
    }

    public function block(User $user): RedirectResponse
    {
        $user->update(['is_blocked' => true]);

        DB::table('sessions')->where('user_id', $user->id)->delete();

        return back()->with('success', 'Utilisateur bloqué.');
    }

    public function unblock(User $user): RedirectResponse
    {
        $user->update(['is_blocked' => false]);

        return back()->with('success', 'Utilisateur débloqué.');
    }

    private function resolveReportable(string $morphClass, int $id): ?Model
    {
        if ($morphClass === Post::class || $morphClass === 'post') {
            return Post::with('user')->find($id);
        }

        if ($morphClass === Comment::class || $morphClass === 'comment') {
            return Comment::with('user')->find($id);
        }

        return null;
    }

    private function resolveReportableByType(string $type, int $id): Model
    {
        return match ($type) {
            'post' => Post::findOrFail($id),
            'comment' => Comment::findOrFail($id),
            default => abort(404),
        };
    }
}
