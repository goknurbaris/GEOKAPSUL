<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $legacyFilter = $request->string('filter')->toString();
        $readFilter = $request->string('read')->toString();
        $typeFilter = $request->string('type')->toString();
        $allowedTypes = ['capsule-created', 'capsule-opened', 'capsule-unlock-reminder'];
        $perPage = min(30, max(1, $request->integer('per_page', 10)));

        if ($legacyFilter === 'unread' && $readFilter === '') {
            $readFilter = 'unread';
        } elseif (in_array($legacyFilter, $allowedTypes, true) && $typeFilter === '') {
            $typeFilter = $legacyFilter;
        }

        $notificationsQuery = $request->user()
            ->notifications()
            ->select(['id', 'type', 'title', 'body', 'action_url', 'read_at', 'created_at']);

        if ($readFilter === 'unread') {
            $notificationsQuery->whereNull('read_at');
        } elseif ($readFilter === 'read') {
            $notificationsQuery->whereNotNull('read_at');
        }

        if (in_array($typeFilter, $allowedTypes, true)) {
            $notificationsQuery->where('type', $typeFilter);
        }

        $notifications = $notificationsQuery->paginate($perPage);

        return response()->json([
            'items' => $notifications->items(),
            'unread_count' => $request->user()->notifications()->whereNull('read_at')->count(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'has_more_pages' => $notifications->hasMorePages(),
            ],
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
