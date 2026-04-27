<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // ── Relations ────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers statiques ────────────────────────────────────

    /**
     * Enregistre une entrée dans le journal.
     *
     * @param  string      $action       ex: 'transaction.created'
     * @param  string      $module       ex: 'transactions'
     * @param  string      $description  texte lisible
     * @param  array       $meta         données extra (optionnel)
     * @param  int|null    $userId       null = prendre le user de session
     */
    public static function record(
        string $action,
        string $module,
        string $description,
        array  $meta   = [],
        ?int   $userId = null
    ): void {
        try {
            $request = request();

            static::create([
                'user_id'     => $userId ?? session('user_id'),
                'action'      => $action,
                'module'      => $module,
                'description' => $description,
                'ip_address'  => $request?->ip(),
                'user_agent'  => $request?->userAgent(),
                'meta'        => $meta ?: null,
            ]);
        } catch (\Throwable $e) {
            // Ne jamais planter l'app à cause d'un log
            \Log::error('ActivityLog::record failed: ' . $e->getMessage());
        }
    }

    // ── Scopes ───────────────────────────────────────────────
    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ── Accesseurs ───────────────────────────────────────────
    public function getModuleLabelAttribute(): string
    {
        return match ($this->module) {
            'auth'                   => 'Authentification',
            'transactions'           => 'Transactions',
            'categories'             => 'Catégories',
            'budgets'                => 'Budgets',
            'savings_goals'          => 'Objectifs d\'épargne',
            'recurring_transactions' => 'Transactions récurrentes',
            'tags'                   => 'Étiquettes',
            'notifications'          => 'Notifications',
            'admin'                  => 'Administration',
            'profile'                => 'Profil',
            default                  => ucfirst($this->module),
        };
    }

    public function getActionColorAttribute(): string
    {
        if (str_contains($this->action, 'created') || str_contains($this->action, 'login'))
            return 'success';
        if (str_contains($this->action, 'deleted') || str_contains($this->action, 'logout'))
            return 'danger';
        if (str_contains($this->action, 'updated') || str_contains($this->action, 'restored'))
            return 'warning';
        return 'secondary';
    }

    public function getActionIconAttribute(): string
    {
        if (str_contains($this->action, 'created'))  return 'fa-plus-circle';
        if (str_contains($this->action, 'updated'))  return 'fa-edit';
        if (str_contains($this->action, 'deleted'))  return 'fa-trash';
        if (str_contains($this->action, 'login'))    return 'fa-sign-in-alt';
        if (str_contains($this->action, 'logout'))   return 'fa-sign-out-alt';
        if (str_contains($this->action, 'restored')) return 'fa-undo';
        if (str_contains($this->action, 'exported')) return 'fa-download';
        return 'fa-circle';
    }
}
