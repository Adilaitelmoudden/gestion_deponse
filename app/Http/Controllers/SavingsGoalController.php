<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        $goals = SavingsGoal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($goal) {
                $goal->percentage   = $goal->target_amount > 0
                    ? min(100, round(($goal->current_amount / $goal->target_amount) * 100, 1))
                    : 0;
                $goal->remaining    = max(0, $goal->target_amount - $goal->current_amount);
                $goal->is_completed = $goal->current_amount >= $goal->target_amount;
                $goal->days_left    = $goal->deadline ? now()->diffInDays($goal->deadline, false) : null;
                return $goal;
            });

        $totalSaved     = $goals->sum('current_amount');
        $totalTargeted  = $goals->sum('target_amount');
        $completedCount = $goals->where('is_completed', true)->count();

        return view('savings_goals.index', compact('goals', 'totalSaved', 'totalTargeted', 'completedCount'));
    }

    public function create()
    {
        return view('savings_goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'target_amount'  => 'required|numeric|min:1',
            'current_amount' => 'nullable|numeric|min:0',
            'deadline'       => 'nullable|date',
            'description'    => 'nullable|string|max:1000',
        ]);

        $validated['user_id']        = session('user_id');
        $validated['current_amount'] = $validated['current_amount'] ?? 0;

        if ($validated['current_amount'] > 0) {
            $validated['history'] = json_encode([[
                'type'   => 'deposit',
                'amount' => $validated['current_amount'],
                'note'   => 'Montant initial',
                'date'   => now()->toDateString(),
            ]]);
        }

        SavingsGoal::create($validated);

        return redirect()->route('savings_goals.index')
            ->with('success', 'Objectif créé avec succès !');
    }

    public function show(SavingsGoal $savings_goal)
    {
        if ($savings_goal->user_id != session('user_id')) {
            abort(403);
        }

        $savings_goal->percentage   = $savings_goal->target_amount > 0
            ? min(100, round(($savings_goal->current_amount / $savings_goal->target_amount) * 100, 1))
            : 0;
        $savings_goal->remaining    = max(0, $savings_goal->target_amount - $savings_goal->current_amount);
        $savings_goal->is_completed = $savings_goal->current_amount >= $savings_goal->target_amount;

        $history = $savings_goal->history ? json_decode($savings_goal->history, true) : [];

        return view('savings_goals.show', [
            'savingsGoal' => $savings_goal,
            'history'     => $history,
        ]);
    }

    public function edit(SavingsGoal $savings_goal)
    {
        if ($savings_goal->user_id != session('user_id')) {
            abort(403);
        }

        return view('savings_goals.edit', ['savingsGoal' => $savings_goal]);
    }

    public function update(Request $request, SavingsGoal $savings_goal)
    {
        if ($savings_goal->user_id != session('user_id')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'deadline'      => 'nullable|date',
            'description'   => 'nullable|string|max:1000',
        ]);

        $savings_goal->update($validated);

        return redirect()->route('savings_goals.index')
            ->with('success', 'Objectif mis à jour !');
    }

    public function destroy(SavingsGoal $savings_goal)
    {
        if ($savings_goal->user_id != session('user_id')) {
            abort(403);
        }

        $savings_goal->delete();

        return redirect()->route('savings_goals.index')
            ->with('success', 'Objectif supprimé.');
    }

    // ── Deposit ──────────────────────────────────────────────
    public function deposit(Request $request, SavingsGoal $savings_goal)
    {
        if ($savings_goal->user_id != session('user_id')) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note'   => 'nullable|string|max:255',
        ]);

        $savings_goal->current_amount += $request->amount;

        $history   = $savings_goal->history ? json_decode($savings_goal->history, true) : [];
        $history[] = [
            'type'   => 'deposit',
            'amount' => (float) $request->amount,
            'note'   => $request->note ?? '',
            'date'   => now()->toDateString(),
        ];
        $savings_goal->history = json_encode($history);
        $savings_goal->save();

        return redirect()->route('savings_goals.show', $savings_goal)
            ->with('success', number_format($request->amount, 2) . ' ' . (Cache::get('admin_system_settings', [])['default_currency'] ?? 'MAD') . ' versés avec succès !');
    }

    // ── Withdraw ─────────────────────────────────────────────
    public function withdraw(Request $request, SavingsGoal $savings_goal)
    {
        if ($savings_goal->user_id != session('user_id')) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $savings_goal->current_amount,
            'note'   => 'nullable|string|max:255',
        ]);

        $savings_goal->current_amount -= $request->amount;

        $history   = $savings_goal->history ? json_decode($savings_goal->history, true) : [];
        $history[] = [
            'type'   => 'withdraw',
            'amount' => (float) $request->amount,
            'note'   => $request->note ?? '',
            'date'   => now()->toDateString(),
        ];
        $savings_goal->history = json_encode($history);
        $savings_goal->save();

        return redirect()->route('savings_goals.show', $savings_goal)
            ->with('success', number_format($request->amount, 2) . ' ' . (Cache::get('admin_system_settings', [])['default_currency'] ?? 'MAD') . ' retirés.');
    }
}
