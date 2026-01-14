<?php

namespace App\Services\Web;

use App\Models\Debt;
use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DebtService
{
    /**
     * Mengambil daftar hutang dengan relasi customer & pencarian
     */
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function getDebts($perPage = 10, $search = null)
    {
        return Debt::with('customer')
            ->where('user_id', auth()->id())
            ->when($search, function ($query, $search) {
                $query->where('reference_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('due_date', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Generate ID Referensi Unik: DBT-YYYYMMDD-0001
     */
    public function generateReferenceId()
    {
        $date = Carbon::now()->format('Ymd');
        $prefix = 'DBT-'.$date.'-';

        $lastDebt = Debt::where('reference_id', 'LIKE', $prefix.'%')
            ->orderBy('reference_id', 'desc')
            ->first();

        if (! $lastDebt) {
            $number = '0001';
        } else {
            $lastNumber = substr($lastDebt->reference_id, -4);
            $number = str_pad((int) $lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix.$number;
    }

    /**
     * Simpan Hutang Baru
     */
    public function createDebt(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['reference_id'] = $this->generateReferenceId();
            $data['user_id'] = auth()->id();
            $data['remaining_amount'] = $data['amount']; // Awalnya sisa = total
            $data['status'] = 'pending';

            return Debt::create($data);
        });
    }

    /**
     * Update Status Hutang Otomatis (Gunakan ini saat ada cicilan masuk)
     */
    public function updateDebtBalance(Debt $debt, $paymentAmount)
    {
        return DB::transaction(function () use ($debt, $paymentAmount) {
            $newRemaining = $debt->remaining_amount - $paymentAmount;

            $status = 'partial';
            if ($newRemaining <= 0) {
                $status = 'paid';
                $newRemaining = 0;
            }

            $debt->update([
                'remaining_amount' => $newRemaining,
                'status' => $status,
            ]);

            return $debt;
        });
    }

    /**
     * Cek Hutang Jatuh Tempo (Expired)
     */
    public function markExpiredDebts()
    {
        return Debt::where('user_id', auth()->id())
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->update(['status' => 'expired']);
    }
}
