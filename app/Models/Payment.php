<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'debt_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'paid_at',
        'tripay_reference',
        'status'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount_paid' => 'decimal:2'
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function customer()
    {
        return $this->hasOneThrough(Customer::class, Debt::class, 'id', 'id', 'debt_id', 'customer_id');
    }

    // Scope for filtering payments
    public function scopeByUser($query, $userId)
    {
        return $query->whereHas('debt', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
