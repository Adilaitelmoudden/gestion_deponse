<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /** GET /admin/notifications/compose */
    public function compose()
    {
        $users = User::orderBy('name')->get();
        return view('admin.notifications.compose', compact('users'));
    }

    /** POST /admin/notifications/send */
    public function send(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'message' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($request->filled('user_id')) {
            // Single user
            Notification::create([
                'user_id' => $request->user_id,
                'title'   => $request->title,
                'message' => $request->message,
                'is_read' => false,
            ]);
            $sent = 1;
        } else {
            // All users
            $users = User::all();
            foreach ($users as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'title'   => $request->title,
                    'message' => $request->message,
                    'is_read' => false,
                ]);
            }
            $sent = $users->count();
        }

        return redirect()->route('admin.notifications.history')
            ->with('success', "Notification envoyée à {$sent} utilisateur(s).");
    }

    /** GET /admin/notifications/history */
    public function history(Request $request)
    {
        $query = Notification::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('is_read')) {
            $query->where('is_read', (bool) $request->is_read);
        }

        $notifications = $query->paginate(20)->withQueryString();
        $users         = User::orderBy('name')->get();

        return view('admin.notifications.history', compact('notifications', 'users'));
    }

    /** DELETE /admin/notifications/{notification} */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification supprimée.');
    }
}
