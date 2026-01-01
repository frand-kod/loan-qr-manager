<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use App\Services\DebtService;
use App\Services\LogService;

class DashboardController extends Controller
{
    public function summary(
        DebtService $debtService,
        CustomerService $customerService
    ) {
        $userId = auth()->id();

        // Get summary statistics
        $stats = $debtService->getDebtStats($userId);
        $customerCount = $customerService->getCustomerCount($userId);

        // Get recent pending debts
        $recentPendingDebts = \App\Models\Debt::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('customer')
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Get recent paid debts
        $recentPaidDebts = \App\Models\Debt::where('user_id', $userId)
            ->where('status', 'paid')
            ->with('customer')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Get top customers by total debt
        $topCustomers = \App\Models\Customer::where('user_id', $userId)
            ->with(['debts' => function($query) {
                $query->select('id', 'customer_id', 'amount', 'status');
            }])
            ->withCount('debts')
            ->get()
            ->map(function($customer) {
                $totalDebt = $customer->debts->sum('amount');
                $pendingDebt = $customer->debts->where('status', 'pending')->sum('amount');
                $paidDebt = $customer->debts->where('status', 'paid')->sum('amount');
                $overdueDebt = $customer->debts->where('status', 'overdue')->sum('amount');

                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'total_debt' => $totalDebt,
                    'pending_debt' => $pendingDebt,
                    'paid_debt' => $paidDebt,
                    'overdue_debt' => $overdueDebt,
                    'debts_count' => $customer->debts_count,
                ];
            })
            ->filter(function($customer) {
                return $customer['total_debt'] > 0;
            })
            ->sortByDesc('total_debt')
            ->take(5);

        return response()->json([
            'summary' => [
                'total_customers' => $customerCount,
                'total_paid' => $stats['total_paid'] ?? 0,
                'total_pending' => $stats['total_pending'] ?? 0,
                'total_overdue' => $stats['total_overdue'] ?? 0,
            ],
            'recent_pending_debts' => $recentPendingDebts,
            'recent_paid_debts' => $recentPaidDebts,
            'top_customers' => $topCustomers->values(),
        ]);
    }
}
