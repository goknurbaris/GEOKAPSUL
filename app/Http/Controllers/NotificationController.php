<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filter = $request->string('filter')->toString();
        $notificationsQuery = $request->user()
            ->notifications()
            ->select(['id', 'type', 'title', 'body', 'action_url', 'read_at', 'created_at']);

        if ($filter === 'unread') {
            $notificationsQuery->whereNull('read_at');
        } elseif (in_array($filter, ['capsule-created', 'capsule-opened'], true)) {
            $notificationsQuery->where('type', $filter);
        }

        $notifications = $notificationsQuery
            ->limit(10)
            ->get();

        return response()->json([
            'items' => $notifications,
            'unread_count' => $request->user()->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function markAllRead(Request $request): Response
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->noContent();
    }
}
