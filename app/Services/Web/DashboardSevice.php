<?php

namespace App\Services\Web;

use App\Models\Customer;
use App\Models\Debt;

class DashboardSevice
{
    public function getStats()
    {
        $userId = auth()->id();

        return [
            'total_customers' => Customer::where('user_id', $userId)->count(),
            // 'total_customers' => 14,
            // Gunakan 'paid' sesuai data kamu
            'total_paid' => (float) Debt::where('user_id', $userId)
                ->where('status', 'paid')
                ->sum('total_amount'),

            // UBAH 'unpaid' MENJADI 'pending' sesuai data kamu
            'total_pending' => (float) Debt::where('user_id', $userId)
                ->where('status', 'pending')
                ->sum('remaining_amount'),

            // UBAH JUGA DI SINI
            'total_overdue' => (float) Debt::where('user_id', $userId)
                ->where('status', 'pending')
                ->where('due_date', '<', now())
                ->sum('remaining_amount'),

        ];
    }
}
