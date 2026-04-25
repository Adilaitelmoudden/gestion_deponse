<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->paginate(20);

        // Mark all as read
        Notification::where('user_id', $userId)->where('is_read', false)->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id != session('user_id')) abort(403);
        $notification->update(['is_read' => true]);
        return back();
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id != session('user_id')) abort(403);
        $notification->delete();
        return back()->with('success', 'Notification supprimée.');
    }

    // JSON endpoint for badge count
    public function unreadCount()
    {
        $count = Notification::where('user_id', session('user_id'))->where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }
}
