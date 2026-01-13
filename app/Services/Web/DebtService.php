<?php

namespace App\Services\Web;

use App\Models\Debt;
use Carbon\Carbon;

class DebtService
{
    public function generateReferenceId()
    {
        $date = Carbon::now()->format('Ymd'); // Hasil: 20240113
        $prefix = 'DBT-'.$date.'-';

        // Cari urutan terakhir di hari ini
        $lastDebt = Debt::where('reference_id', 'LIKE', $prefix.'%')
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastDebt) {
            $number = '0001';
        } else {
            // Ambil 4 angka terakhir, tambah 1
            $lastNumber = substr($lastDebt->reference_id, -4);
            $number = str_pad((int) $lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix.$number;
    }

    public function createDebt(array $data)
    {
        $data['reference_id'] = $this->generateReferenceId();
        $data['user_id'] = auth()->id();
        $data['remaining_amount'] = $data['amount']; // Awalnya sisa hutang = total hutang

        return Debt::create($data);
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

    public function sendManualReminder($debtId)
    {
        $debt = Debt::with('customer')->findOrFail($debtId);
        $waService = app(\App\Services\WhatsappService::class);

        $amount = number_format($debt->remaining_amount, 0, ',', '.');
        $dueDate = Carbon::parse($debt->due_date)->translatedFormat('d F Y');

        // Template Pesan
        $message = "Halo *{$debt->customer->name}*,\n\n";
        $message .= "Kami menginformasikan mengenai tagihan Anda dengan referensi *{$debt->reference_id}*.\n";
        $message .= "Sisa tagihan: *Rp {$amount}*\n";
        $message .= "Jatuh tempo: *{$dueDate}*\n\n";
        $message .= 'Mohon segera melakukan pembayaran. Jika sudah membayar, abaikan pesan ini. Terima kasih.';

        $status = $waService->sendMessage($debt->customer->whatsapp_number, $message, $debt->id);

        if ($status) {
            $debt->update(['last_reminder_sent' => now()]);

            return true;
        }

        return false;
    }
}
