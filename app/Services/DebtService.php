<?php

namespace App\Services;

use App\Models\Debt;
use Illuminate\Support\Str;

class DebtService
{
    protected $logService;

    // Inject LogService ke dalam DebtService
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function createDebt(array $data)
    {
        $debt = Debt::create([
            'customer_id' => $data['customer_id'],
            'amount' => $data['amount'],
            'due_date' => $data['due_date'],
            'reference_id' => 'INV-'.Str::upper(Str::random(10)),
            'status' => 'pending',
        ]);
        // panggil record
        $this->logService->record(
            'Buat utang baru Rp '.number_format($data['amount']),
            'Debt',
            $debt->id
        );

        return $debt;
    }

    public function getAllDebts($userId)
    {
        return Debt::whereHas('customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('customer')->latest()->paginate(10);
    }

    public function getDebtById($id, $userId)
    {
        return Debt::whereHas('customer', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('customer')->find($id);
    }

    public function updateDebt($id, array $data, $userId)
    {
        $debt = $this->getDebtById($id, $userId);

        if (!$debt) {
            return null;
        }

        $debt->update($data);

        // Log the update
        $this->logService->record(
            'Update utang Rp ' . number_format($data['amount']),
            'Debt',
            $debt->id
        );

        return $debt->fresh('customer');
    }

    public function deleteDebt($id, $userId)
    {
        $debt = $this->getDebtById($id, $userId);

        if (!$debt) {
            return false;
        }

        // Log before deleting
        $this->logService->record(
            'Hapus utang Rp ' . number_format($debt->amount),
            'Debt',
            $debt->id
        );

        $debt->delete();
        return true;
    }

    public function getDebtStats($userId)
    {
        $query = Debt::whereHas('customer', fn ($q) => $q->where('user_id', $userId));

        return [
            'total_pending' => (int) $query->clone()->where('status', 'pending')->sum('amount'),
            'count_pending' => $query->clone()->where('status', 'pending')->count(),
            'total_paid' => (int) $query->clone()->where('status', 'paid')->sum('amount'),
        ];
    }
}
