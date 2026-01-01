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

    public function show($id)
    {
        $debt = $this->debtService->getDebtById($id, auth()->id());

        if (!$debt) {
            return response()->json(['message' => 'Debt not found'], 404);
        }

        return response()->json($debt);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:1000',
            'due_date' => 'required|date',
            'status' => 'sometimes|in:pending,paid,overdue',
        ]);

        $debt = $this->debtService->updateDebt($id, $validated, auth()->id());

        if (!$debt) {
            return response()->json(['message' => 'Debt not found'], 404);
        }

        return response()->json([
            'message' => 'Debt updated successfully',
            'data' => $debt,
        ]);
    }

    public function destroy($id)
    {
        $result = $this->debtService->deleteDebt($id, auth()->id());

        if (!$result) {
            return response()->json(['message' => 'Debt not found'], 404);
        }

        return response()->json(['message' => 'Debt deleted successfully']);
    }
}
