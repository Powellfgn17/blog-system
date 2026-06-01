<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Notification::class);

        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => $this->getUnreadCount($request),
            ]);
        }

        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Notification::class);

        return response()->json([
            'count' => $this->getUnreadCount($request),
        ]);
    }

    public function readAll(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('viewAny', Notification::class);

        Notification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => 0]);
        }

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function markRead(Request $request, Notification $notification): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $notification);

        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('notifications.index');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'url' => $url,
                'unread_count' => $this->getUnreadCount($request),
            ]);
        }

        return redirect($url);
    }

    private function getUnreadCount(Request $request): int
    {
        return Notification::query()
            ->where('user_id', $request->user()->id)
            ->unread()
            ->count();
    }
}
