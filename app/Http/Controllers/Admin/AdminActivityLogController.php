<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    /**
     * GET /admin/activity-logs
     * Liste paginée des logs avec filtres.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filtres
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                  ->orWhere('action', 'like', "%$search%")
                  ->orWhere('ip_address', 'like', "%$search%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Pour les filtres dropdown
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $users   = User::orderBy('name')->get(['id', 'name', 'email']);

        // Stats rapides (dernières 24h)
        $statsToday = [
            'total'    => ActivityLog::whereDate('created_at', today())->count(),
            'logins'   => ActivityLog::whereDate('created_at', today())->where('action', 'auth.login')->count(),
            'created'  => ActivityLog::whereDate('created_at', today())->where('action', 'like', '%.created')->count(),
            'deleted'  => ActivityLog::whereDate('created_at', today())->where('action', 'like', '%.deleted')->count(),
        ];

        return view('admin.activity_logs.index', compact(
            'logs', 'modules', 'users', 'statsToday'
        ));
    }

    /**
     * DELETE /admin/activity-logs/purge
     * Supprimer les anciens logs (> N jours).
     */
    public function purge(Request $request)
    {
        $request->validate(['days' => 'required|integer|min:7|max:365']);
        $count = ActivityLog::where('created_at', '<', now()->subDays($request->days))->delete();

        return back()->with('success', "$count entrées supprimées (antérieures à {$request->days} jours).");
    }
}
