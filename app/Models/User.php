<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'last_login_at', 'balance'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active'         => 'boolean',
        'last_login_at'     => 'datetime',
        'balance'           => 'float',
    ];

    // Vérifier si l'utilisateur est admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Vérifier si l'utilisateur est actif
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Calculer le solde réel = balance initiale + revenus – dépenses
     */
    public function getComputedBalance(): float
    {
        $income  = $this->transactions()->where('type', 'income')->sum('amount');
        $expense = $this->transactions()->where('type', 'expense')->sum('amount');
        return (float) ($this->balance + $income - $expense);
    }

    /**
     * Vérifier si une dépense est possible (solde après >= 0)
     */
    public function canAfford(float $amount): bool
    {
        return ($this->getComputedBalance() - $amount) >= 0;
    }

    // Relations
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function savingsGoals()
    {
        return $this->hasMany(SavingsGoal::class);
    }
}