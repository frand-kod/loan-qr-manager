<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use App\Services\DebtService;
use App\Services\LogService;

class DashboardController extends Controller
{
    //
    public function index(
        DebtService $debtService,
        CustomerService $customerService,
        LogService $logService
    ) {
        $userId = auth()->id();

        return response()->json([
            'status' => 'success',
            'data' => [
                'stats' => $debtService->getDebtStats($userId),
                'customer_count' => $customerService->getCustomerCount($userId),
                'recent_logs' => $logService->getRecentLogs($userId),
            ],
        ]);
    }
}
