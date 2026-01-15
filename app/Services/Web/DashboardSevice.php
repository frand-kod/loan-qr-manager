<?php

namespace App\Services\Web;

use App\Models\Customer;
use App\Models\Debt;

class DashboardSevice
{
    public function getStats()
    {
        $userId = auth()->id();
        $now = now()->format('Y-m-d');

        // Mengambil semua statistik Debt dalam satu kali jalan
        $debtStats = Debt::where('user_id', $userId)
            ->selectRaw("
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN status IN ('pending', 'partial') THEN remaining_amount ELSE 0 END) as total_pending,
            SUM(CASE WHEN status IN ('pending', 'partial') AND due_date < ? THEN remaining_amount ELSE 0 END) as total_overdue
        ", [$now])
            ->first();
        $topCustomers = Customer::where('user_id', $userId)
            ->withSum(['debts as total_debt' => function ($query) {
                $query->whereIn('status', ['pending', 'partial']);
            }], 'remaining_amount')
            ->orderByDesc('total_debt')
            ->take(5)
            ->get();
        $blacklistCustomers = Customer::where('customer_flag', 'blacklist')
            ->select('id', 'name', 'whatsapp_number')
            ->limit(5)
            ->get();

        return [
            'total_customers' => Customer::where('user_id', $userId)->count(),
            'total_paid' => (float) ($debtStats->total_paid ?? 0),
            'total_pending' => (float) ($debtStats->total_pending ?? 0),
            'total_overdue' => (float) ($debtStats->total_overdue ?? 0),
            'top_customers' => $topCustomers,
            'blacklist_customers' => $blacklistCustomers,
        ];
    }
}
