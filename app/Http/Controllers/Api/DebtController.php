<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DebtService;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    //
    protected $debtService;

    public function __construct(DebtService $debtService)
    {
        $this->debtService = $debtService;
    }

    public function index()
    {
        $debts = $this->debtService->getAllDebts(auth()->id());

        return response()->json($debts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:1000',
            'due_date' => 'required|date|after:today',
        ]);

        $debt = $this->debtService->createDebt($validated);

        return response()->json([
            'message' => 'Debt recorded',
            'data' => $debt,
        ], 201);
    }
}
