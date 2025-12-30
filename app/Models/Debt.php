<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'amount',
        'due_date',
        'reference_id',
        'status',
        'last_reminder_sent',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // $debts = Debt::where('status', 'pending')->with('customer')->get();

    public static function checkPending()
    {
        return self::where('status', 'pending')->with('customer')->get();

    }
}
