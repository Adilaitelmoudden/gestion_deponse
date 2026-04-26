<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminExportController extends Controller
{
    /** GET /admin/export */
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('admin.export.index', compact('users'));
    }

    /** GET /admin/export/users */
    public function exportUsers()
    {
        $users = User::withCount('transactions')
            ->with('transactions')
            ->get()
            ->map(function ($user) {
                $income  = $user->transactions->where('type', 'income')->sum('amount');
                $expense = $user->transactions->where('type', 'expense')->sum('amount');
                return [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'role'              => $user->role,
                    'status'            => $user->is_active ? 'Actif' : 'Inactif',
                    'transactions_count'=> $user->transactions_count,
                    'total_income'      => number_format($income, 2),
                    'total_expense'     => number_format($expense, 2),
                    'balance'           => number_format($income - $expense, 2),
                    'created_at'        => $user->created_at?->format('d/m/Y H:i'),
                ];
            });

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Nom', 'Email', 'Rôle', 'Statut', 'Nb Transactions', 'Revenus', 'Dépenses', 'Solde', 'Inscrit le']);

            foreach ($users as $row) {
                fputcsv($file, array_values($row));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** GET /admin/export/transactions */
    public function exportTransactions(Request $request)
    {
        $query = Transaction::with(['user', 'category'])->latest('date');

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $transactions = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transactions_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Utilisateur', 'Email', 'Catégorie', 'Type', 'Montant', 'Description', 'Date', 'Créé le']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->user?->name    ?? '-',
                    $t->user?->email   ?? '-',
                    $t->category?->name ?? '-',
                    $t->type === 'income' ? 'Revenu' : 'Dépense',
                    number_format($t->amount, 2),
                    $t->description ?? '',
                    $t->date?->format('d/m/Y'),
                    $t->created_at?->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
