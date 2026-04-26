<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaction;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class RecurringTransactionController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $recurring = RecurringTransaction::where('user_id', $userId)
            ->with('category')
            ->orderBy('next_due_date')
            ->get();

        return view('recurring_transactions.index', compact('recurring'));
    }

    public function create()
    {
        $userId = session('user_id');
        $categories = Category::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('is_default', true);
        })->get();

        return view('recurring_transactions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $userId = session('user_id');

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'type'        => 'required|in:income,expense',
            'frequency'   => 'required|in:daily,weekly,monthly,yearly',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after:start_date',
        ]);

        $validated['user_id']       = $userId;
        $validated['next_due_date'] = $validated['start_date'];
        $validated['is_active']     = true;

        RecurringTransaction::create($validated);

        return redirect()->route('recurring.index')
            ->with('success', 'Transaction récurrente créée avec succès!');
    }

    public function edit(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        $userId = session('user_id');
        $categories = Category::where(function ($q) use ($userId) {
            $q->where('user_id', $userId)->orWhere('is_default', true);
        })->get();

        return view('recurring_transactions.edit', compact('recurring', 'categories'));
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
            'type'        => 'required|in:income,expense',
            'frequency'   => 'required|in:daily,weekly,monthly,yearly',
            'end_date'    => 'nullable|date',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $recurring->update($validated);

        return redirect()->route('recurring.index')
            ->with('success', 'Transaction récurrente mise à jour!');
    }

    public function destroy(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);
        $recurring->delete();

        return redirect()->route('recurring.index')
            ->with('success', 'Transaction récurrente supprimée.');
    }

    // Execute (record) a recurring transaction now
    public function execute(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        Transaction::create([
            'user_id'     => $recurring->user_id,
            'category_id' => $recurring->category_id,
            'amount'      => $recurring->amount,
            'description' => $recurring->description . ' (récurrent)',
            'date'        => now()->toDateString(),
            'type'        => $recurring->type,
        ]);

        // Advance next_due_date
        $next = match ($recurring->frequency) {
            'daily'   => $recurring->next_due_date->addDay(),
            'weekly'  => $recurring->next_due_date->addWeek(),
            'monthly' => $recurring->next_due_date->addMonth(),
            'yearly'  => $recurring->next_due_date->addYear(),
        };

        // Deactivate if past end_date
        if ($recurring->end_date && $next->isAfter($recurring->end_date)) {
            $recurring->is_active = false;
        }

        $recurring->next_due_date = $next;
        $recurring->save();

        return redirect()->route('recurring.index')
            ->with('success', 'Transaction enregistrée avec succès!');
    }

    private function authorize(RecurringTransaction $recurring)
    {
        if ($recurring->user_id != session('user_id')) {
            abort(403, 'Accès non autorisé.');
        }
    }
}
