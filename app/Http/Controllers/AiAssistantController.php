<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use App\Models\SavingsGoal;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function index()
    {
        return view('assistant.index');
    }

    public function chat(Request $request)
    {
        // Always return JSON — no matter what happens
        try {
            $validated = $request->validate([
                'message'  => 'required|string|max:1000',
                'history'  => 'nullable|array|max:20',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'reply' => '⚠️ الرسالة غير صالحة: ' . implode(' ', $e->validator->errors()->all()),
            ], 422);
        }

        try {
            $userId = session('user_id');
            if (!$userId) {
                return response()->json([
                    'reply'    => '⚠️ انتهات جلستك. أعد تسجيل الدخول.',
                    'redirect' => route('login'),
                ], 401);
            }

            $context      = $this->buildFinancialContext($userId);
            $systemPrompt = $this->buildSystemPrompt($context);

            $apiKey = env('GROQ_API_KEY', '');

            if (empty($apiKey)) {
                return response()->json(['reply' => '⚠️ GROQ_API_KEY مازال ما تزادش فـ ملف .env — زيدها وكتب: php artisan config:clear']);
            }

            // Groq uses OpenAI-compatible API
            $messages = [];
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];

            foreach (($request->history ?? []) as $turn) {
                if (isset($turn['role'], $turn['content']) && in_array($turn['role'], ['user', 'assistant'])) {
                    $messages[] = ['role' => $turn['role'], 'content' => (string) $turn['content']];
                }
            }
            $messages[] = ['role' => 'user', 'content' => $request->message];

            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'       => 'llama-3.3-70b-versatile',
                    'max_tokens'  => 1024,
                    'temperature' => 0.7,
                    'messages'    => $messages,
                ]);

            if ($response->failed()) {
                $status = $response->status();
                $errMsg = $response->json()['error']['message'] ?? $response->body();
                Log::error('Groq API failed', ['status' => $status, 'body' => $response->body()]);
                $msg = match(true) {
                    $status === 400 => '⚠️ طلب غلوط: ' . $errMsg,
                    $status === 401 => '⚠️ GROQ_API_KEY غلوطة أو ما عندهاش صلاحية.',
                    $status === 429 => '⚠️ وصلت للحد الأقصى ديال Groq. استنى دقيقة وحاول.',
                    $status >= 500  => '⚠️ مشكلة فـ سيرفر Groq. حاول بعد قليل.',
                    default         => '⚠️ خطأ HTTP ' . $status . ': ' . $errMsg,
                };
                return response()->json(['reply' => $msg]);
            }

            $data = $response->json();

            if (!is_array($data)) {
                Log::error('Groq returned non-JSON', ['body' => $response->body()]);
                return response()->json(['reply' => '⚠️ جواب غير صالح من Groq.']);
            }

            if (isset($data['error'])) {
                $errMsg = $data['error']['message'] ?? 'خطأ غير معروف';
                return response()->json(['reply' => '⚠️ Groq: ' . $errMsg]);
            }

            $text = $data['choices'][0]['message']['content'] ?? null;

            if ($text === null) {
                Log::error('Groq response missing content', ['data' => $data]);
                return response()->json(['reply' => '⚠️ ما وصلتش جواب من Groq.']);
            }

            return response()->json(['reply' => $text]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Groq connection error: ' . $e->getMessage());
            return response()->json(['reply' => '⚠️ ما قدرناش نوصلو لـ Groq. تحقق من الاتصال بالإنترنت.']);
        } catch (\Exception $e) {
            Log::error('AiAssistant error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['reply' => '⚠️ خطأ داخلي: ' . $e->getMessage()]);
        }
    }

    private function buildFinancialContext(int $userId): array
    {
        $now          = now();
        $currentMonth = $now->month;
        $currentYear  = $now->year;
        $prevMonth    = $now->copy()->subMonth()->month;
        $prevYear     = $now->copy()->subMonth()->year;

        $income  = Transaction::where('user_id', $userId)->where('type', 'income')
            ->whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->sum('amount');
        $expense = Transaction::where('user_id', $userId)->where('type', 'expense')
            ->whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->sum('amount');
        $balance = $income - $expense;

        $prevIncome  = Transaction::where('user_id', $userId)->where('type', 'income')
            ->whereMonth('date', $prevMonth)->whereYear('date', $prevYear)->sum('amount');
        $prevExpense = Transaction::where('user_id', $userId)->where('type', 'expense')
            ->whereMonth('date', $prevMonth)->whereYear('date', $prevYear)->sum('amount');

        $topCategories = Transaction::where('user_id', $userId)->where('type', 'expense')
            ->whereMonth('date', $currentMonth)->whereYear('date', $currentYear)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')->orderByDesc('total')->take(5)->get()
            ->map(fn($r) => ['category' => $r->category->name ?? 'N/A', 'total' => round($r->total, 2)]);

        $recent = Transaction::where('user_id', $userId)->with('category')
            ->latest('date')->take(10)->get()
            ->map(fn($t) => [
                'date'     => $t->date->format('d/m/Y'),
                'type'     => $t->type,
                'amount'   => round($t->amount, 2),
                'category' => $t->category->name ?? 'N/A',
                'desc'     => $t->description ?? '',
            ]);

        $budgets = Budget::where('user_id', $userId)
            ->where('month', $currentMonth)->where('year', $currentYear)
            ->with('category')->get()
            ->map(function ($b) use ($userId, $currentMonth, $currentYear) {
                $spent = Transaction::where('user_id', $userId)
                    ->where('category_id', $b->category_id)->where('type', 'expense')
                    ->whereMonth('date', $currentMonth)->whereYear('date', $currentYear)->sum('amount');
                return [
                    'category'  => $b->category->name ?? 'N/A',
                    'budget'    => round($b->amount, 2),
                    'spent'     => round($spent, 2),
                    'remaining' => round($b->amount - $spent, 2),
                    'pct'       => $b->amount > 0 ? round(($spent / $b->amount) * 100) : 0,
                ];
            });

        $goals = SavingsGoal::where('user_id', $userId)->get()
            ->map(fn($g) => [
                'name'    => $g->name,
                'target'  => round($g->target_amount, 2),
                'current' => round($g->current_amount, 2),
                'pct'     => $g->target_amount > 0 ? round(($g->current_amount / $g->target_amount) * 100) : 0,
            ]);

        $recurring = RecurringTransaction::where('user_id', $userId)->where('is_active', true)->get()
            ->map(fn($r) => ['name' => $r->name, 'amount' => round($r->amount, 2), 'frequency' => $r->frequency]);

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $mIncome  = Transaction::where('user_id', $userId)->where('type', 'income')
                ->whereMonth('date', $m)->whereYear('date', $currentYear)->sum('amount');
            $mExpense = Transaction::where('user_id', $userId)->where('type', 'expense')
                ->whereMonth('date', $m)->whereYear('date', $currentYear)->sum('amount');
            if ($mIncome > 0 || $mExpense > 0) {
                $monthly[] = ['month' => $m, 'income' => round($mIncome, 2), 'expense' => round($mExpense, 2)];
            }
        }

        return compact(
            'income', 'expense', 'balance',
            'prevIncome', 'prevExpense',
            'topCategories', 'recent',
            'budgets', 'goals', 'recurring', 'monthly',
            'currentMonth', 'currentYear'
        );
    }

    private function buildSystemPrompt(array $ctx): string
    {
        $monthNames = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                       'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $month      = $monthNames[$ctx['currentMonth']];
        $year       = $ctx['currentYear'];

        $budgetsJson   = json_encode($ctx['budgets'],       JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $goalsJson     = json_encode($ctx['goals'],         JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $topCatsJson   = json_encode($ctx['topCategories'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $recentJson    = json_encode($ctx['recent'],        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $monthlyJson   = json_encode($ctx['monthly'],       JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $recurringJson = json_encode($ctx['recurring'],     JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $balanceTrend = $ctx['income'] - $ctx['expense'];
        $savingsRate  = $ctx['income'] > 0 ? round(($balanceTrend / $ctx['income']) * 100, 1) : 0;
        $prevBalance  = $ctx['prevIncome'] - $ctx['prevExpense'];

        return "Tu es un assistant financier personnel intégré dans une application de gestion des dépenses.
Tu parles français et darija marocaine (mix naturel). Sois concis, chaleureux et utile.
Tu as accès aux données financières RÉELLES de l'utilisateur ci-dessous.

══════════════ DONNÉES DU MOIS EN COURS ({$month} {$year}) ══════════════

RÉSUMÉ :
- Revenus    : {$ctx['income']} DH
- Dépenses   : {$ctx['expense']} DH
- Solde      : {$ctx['balance']} DH  ({$savingsRate}% taux d'épargne)
- Mois préc. : revenus {$ctx['prevIncome']} DH / dépenses {$ctx['prevExpense']} DH / solde {$prevBalance} DH

TOP CATÉGORIES DE DÉPENSES :
{$topCatsJson}

BUDGETS :
{$budgetsJson}

OBJECTIFS D'ÉPARGNE :
{$goalsJson}

TRANSACTIONS RÉCENTES (10 dernières) :
{$recentJson}

TRANSACTIONS RÉCURRENTES ACTIVES :
{$recurringJson}

HISTORIQUE MENSUEL {$year} :
{$monthlyJson}

══════════════════════════════════════════════════════════

RÈGLES :
1. Réponds UNIQUEMENT sur la base de ces données réelles. Ne fabrique rien.
2. Si l'utilisateur pose une question et les données suffisent, donne une réponse précise avec les chiffres.
3. Sois bref (3-5 lignes max) sauf si l'utilisateur demande une analyse détaillée.
4. Pour les conseils, base-toi sur les données (ex: si budget Resto dépassé, conseille de réduire).
5. Tu peux mélanger darija et français naturellement.
6. Utilise des emojis sobrement pour rendre les réponses plus lisibles.
7. Si une information n'est pas dans les données (ex: dépenses d'une année passée non fournie), dis-le clairement.";
    }
}