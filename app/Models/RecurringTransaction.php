<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'amount', 'description',
        'type', 'frequency', 'start_date', 'next_due_date',
        'end_date', 'is_active',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'next_due_date' => 'date',
        'end_date'      => 'date',
        'is_active'     => 'boolean',
        'amount'        => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFrequencyLabelAttribute()
    {
        return match ($this->frequency) {
            'daily'   => 'Quotidien',
            'weekly'  => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'yearly'  => 'Annuel',
            default   => ucfirst($this->frequency),
        };
    }
}
