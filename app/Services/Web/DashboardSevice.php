<?php

namespace App\Services\Web;

use App\Models\Customer;

class DashboardSevice
{
    public function getStats()
    {
        $userId = auth()->id();

        return [
            'total_customers' => Customer::where('user_id', $userId)->count(),
            'total_paid' => 0, // Sementara 0 sampai modul Hutang jadi
            'total_pending' => 0,
            'total_overdue' => 0,
        ];
    }
}
