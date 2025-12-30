<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    //
    protected $fillable = [
        'user_id',
        'name',
        'whatsapp_number',
        'customer_flag',
    ];

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }
}
