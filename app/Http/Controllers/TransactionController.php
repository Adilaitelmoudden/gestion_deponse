<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    private function getCurrency(): string
    {
        return Cache::get('admin_system_settings', [])['default_currency'] ?? 'MAD';
    }
    public function index(Request $request)
    {
        $userId = session('user_id');

        // BUG FIX: wrap orWhere in closure to correctly scope it per user
        $query = Transaction::where('user_id', $userId)->with('category');

        if ($request->filled('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // BUG FIX: orWhere without wrapping leaks across user_id scope — now fixed
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('amount', 'like', '%' . $search . '%');
            });
        }

        // NEW: amount range filter
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }

        // NEW: sort support
        $sortField = in_array($request->sort, ['date', 'amount', 'created_at']) ? $request->sort : 'date';
        $sortDir   = $request->sort_dir === 'asc' ? 'asc' : 'desc';

        // Export CSV before paginating
        if ($request->has('export') && $request->export === 'csv') {
            return $this->exportCsv((clone $query)->orderBy($sortField, $sortDir)->get());
        }

        $transactions = $query->orderBy($sortField, $sortDir)->paginate(15)->withQueryString();

        $categories = Category::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('is_default', true);
        })->get();

        $totalIncome  = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');

        return view('transactions.index', compact(
            'transactions', 'categories',
            'totalIncome', 'totalExpense'
        ));
    }

    public function create()
    {
        $userId = session('user_id');
        $categories = Category::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('is_default', true);
        })->get();

        return view('transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'date'        => 'required|date',
            'type'        => 'required|in:income,expense',
        ]);

        $userId = session('user_id');
        $validated['user_id'] = $userId;

        // ✅ SOLDE : bloquer si dépense dépasse le solde disponible
        if ($validated['type'] === 'expense') {
            $user = \App\Models\User::find($userId);
            if ($user && !$user->canAfford((float) $validated['amount'])) {
                return back()
                    ->withInput()
                    ->with('error', '❌ Opération bloquée : solde insuffisant. Solde disponible : ' . number_format($user->getComputedBalance(), 2) . ' ' . $this->getCurrency() . '.');
            }
        }

        Transaction::create($validated);

        // NEW: budget alert notification
        $this->checkBudgetAlert($validated['user_id'], $validated['category_id'], $validated['date']);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction ajoutée avec succès!');
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->user_id != session('user_id')) {
            abort(403, 'Accès non autorisé.');
        }
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        if ($transaction->user_id != session('user_id')) {
            abort(403, 'Accès non autorisé.');
        }

        $userId = session('user_id');
        $categories = Category::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('is_default', true);
        })->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id != session('user_id')) {
            abort(403, 'Accès non autorisé.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'date'        => 'required|date',
            'type'        => 'required|in:income,expense',
        ]);

        // ✅ SOLDE : vérifier après update si type=expense ou si type change vers expense
        $newType = $validated['type'];
        $newAmount = (float) $validated['amount'];
        $oldType = $transaction->type;
        $oldAmount = (float) $transaction->amount;

        if ($newType === 'expense') {
            $userId = session('user_id');
            $user = \App\Models\User::find($userId);
            // Simuler le solde sans cette transaction puis avec la nouvelle valeur
            $balanceWithoutThis = $user->getComputedBalance();
            if ($oldType === 'expense') {
                $balanceWithoutThis += $oldAmount; // annuler l'ancienne dépense
            } elseif ($oldType === 'income') {
                $balanceWithoutThis -= $oldAmount; // annuler l'ancien revenu
            }
            if (($balanceWithoutThis - $newAmount) < 0) {
                return back()
                    ->withInput()
                    ->with('error', '❌ Modification bloquée : solde insuffisant pour cette dépense.');
            }
        }

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction modifiée avec succès!');
    }

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id != session('user_id')) {
            abort(403, 'Accès non autorisé.');
        }

        // ✅ SOLDE : bloquer suppression d'un revenu si ça rend le solde négatif
        if ($transaction->type === 'income') {
            $user = \App\Models\User::find(session('user_id'));
            $balanceAfter = $user->getComputedBalance() - (float) $transaction->amount;
            if ($balanceAfter < 0) {
                return redirect()->route('transactions.index')
                    ->with('error', '❌ Suppression bloquée : supprimer ce revenu rendrait votre solde négatif (' . number_format($balanceAfter, 2) . ' ' . $this->getCurrency() . ').');
            }
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction supprimée avec succès!');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:transactions,id',
        ]);

        $userId = session('user_id');

        // ✅ SOLDE : vérifier si la suppression groupée rendrait le solde négatif
        $incomeToRemove = Transaction::whereIn('id', $request->ids)
            ->where('user_id', $userId)
            ->where('type', 'income')
            ->sum('amount');

        if ($incomeToRemove > 0) {
            $user = \App\Models\User::find($userId);
            $balanceAfter = $user->getComputedBalance() - (float) $incomeToRemove;
            if ($balanceAfter < 0) {
                return redirect()->route('transactions.index')
                    ->with('error', '❌ Suppression groupée bloquée : ces transactions contiennent des revenus qui rendraient votre solde négatif (' . number_format($balanceAfter, 2) . ' ' . $this->getCurrency() . ').');
            }
        }

        Transaction::whereIn('id', $request->ids)
            ->where('user_id', $userId)
            ->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transactions supprimées avec succès!');
    }

    // NEW: Export filtered transactions as CSV
    private function exportCsv($transactions)
    {
        $filename = 'transactions_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel
            fputcsv($handle, ['Date', 'Description', 'Catégorie', 'Type', 'Montant (' . $this->getCurrency() . ')'], ';');
            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->date->format('d/m/Y'),
                    $t->description ?? '',
                    $t->category->name ?? '',
                    $t->type === 'income' ? 'Revenu' : 'Dépense',
                    number_format($t->amount, 2, ',', ' '),
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // NEW: Auto-notification when budget threshold crossed
    private function checkBudgetAlert($userId, $categoryId, $date)
    {
        try {
            $month = date('m', strtotime($date));
            $year  = date('Y', strtotime($date));

            $budget = \App\Models\Budget::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if (!$budget) return;

            $spent = Transaction::where('user_id', $userId)
                ->where('category_id', $categoryId)
                ->where('type', 'expense')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->sum('amount');

            $pct = $budget->amount > 0 ? ($spent / $budget->amount) * 100 : 0;
            $category = Category::find($categoryId);

            if ($pct >= 100) {
                \App\Models\Notification::firstOrCreate(
                    ['user_id' => $userId, 'title' => '🔴 Budget dépassé : ' . ($category->name ?? '')],
                    ['message' => "Budget \"{$category->name}\" dépassé ({$month}/{$year}). Dépensé : " . number_format($spent, 2) . " " . $this->getCurrency() . " / " . number_format($budget->amount, 2) . " " . $this->getCurrency() . ".", 'is_read' => false]
                );
            } elseif ($pct >= 80) {
                \App\Models\Notification::firstOrCreate(
                    ['user_id' => $userId, 'title' => '🟡 Budget à 80% : ' . ($category->name ?? '')],
                    ['message' => "Budget \"{$category->name}\" à {$pct}% ({$month}/{$year}). Dépensé : " . number_format($spent, 2) . " " . $this->getCurrency() . " / " . number_format($budget->amount, 2) . " " . $this->getCurrency() . ".", 'is_read' => false]
                );
            }
        } catch (\Exception $e) {
            // Never break main flow
        }
    }
}
