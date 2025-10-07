<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();
        
        $query = Notification::where('author_id', $userId)
                           ->orderBy('created_at', 'desc');

        // Filter by read status
        if ($request->has('is_read') && $request->is_read !== null) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        $notifications = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('author_id', Auth::id())
                           ->where('is_read', false)
                           ->count();

        return response()->json([
            'status' => 'success',
            'data' => ['unread_count' => $count]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = Notification::where('id', $id)
                                  ->where('author_id', Auth::id())
                                  ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read',
            'data' => $notification
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('author_id', Auth::id())
                   ->where('is_read', false)
                   ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id): JsonResponse
    {
        $notification = Notification::where('id', $id)
                                  ->where('author_id', Auth::id())
                                  ->first();

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Create notification (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can create notifications'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|max:50',
            'recipients' => 'nullable|array',
            'recipients.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $recipients = $request->recipients ?? [\App\Models\User::pluck('id')->toArray()];
        
        // If no specific recipients, send to all users
        if (empty($recipients)) {
            $recipients = \App\Models\User::pluck('id')->toArray();
        }

        $notifications = [];
        foreach ($recipients as $recipientId) {
            $notifications[] = Notification::create([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type ?? 'info',
                'author_id' => $recipientId,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Notifications sent successfully',
            'data' => [
                'recipients_count' => count($recipients),
                'notifications' => $notifications
            ]
        ], 201);
    }
}
