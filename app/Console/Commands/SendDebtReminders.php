<?php

namespace App\Console\Commands;

use App\Models\Debt;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDebtReminders extends Command
{
    protected $signature = 'reminder:send-wa';

    protected $description = 'Kirim pengingat otomatis berdasarkan jatuh tempo';

    public function handle(WhatsappService $waService)
    {
        $today = Carbon::today()->startOfDay();
        $debts = Debt::checkPending();

        foreach ($debts as $debt) {
            $dueDate = Carbon::parse($debt->due_date)->startOfDay();

            // Parameter kedua 'false' memastikan:
            // Jika Due Date (7 Jan) > Today (30 Des) = Hasilnya POSITIF 7
            $diff = $today->diffInDays($dueDate, false);

            $label = match ((int) $diff) {
                7 => 'H-7',
                3 => 'H-3',
                0 => 'Hari H',
                -3 => 'H+3',
                default => null,
            };

            if ($label) {
                $msg = "Halo {$debt->customer->name}, tagihan Rp ".number_format($debt->amount)." jatuh tempo pada {$debt->due_date}.";

                $waService->sendMessage($debt->customer->whatsapp_number, $msg, $debt->id);
                $debt->update(['last_reminder_sent' => $label]);
            }
        }

        return Command::SUCCESS;
    }

    //     public function handle(WhatsappService $waService)
    //     {
    //         $today = Carbon::today()->startOfDay();
    //
    //         // Ambil utang yang statusnya 'pending'
    //         // $debts = Debt::where('status', 'pending')->with('customer')->get();
    //         $debts = Debt::checkPending();
    //
    //         foreach ($debts as $debt) {
    //             // Pastikan due_date juga dihitung dari awal hari
    //             $dueDate = Carbon::parse($debt->due_date)->startOfDay();
    //
    //             // diffInDays secara default mengembalikan absolute,
    //             // gunakan false sebagai parameter kedua untuk mendukung angka negatif (H+)
    //             $diff = $today->diffInDays($dueDate, false);
    //
    //             $label = null;
    //
    //             // Debugging: uncomment baris di bawah ini jika masih gagal untuk melihat selisih di terminal
    //             // $this->info("Hutang ID {$debt->id} selisih: $diff hari");
    //
    //             if ($diff === 7) {
    //                 $label = 'H-7';
    //             } elseif ($diff === 3) {
    //                 $label = 'H-3';
    //             } elseif ($diff === 0) {
    //                 $label = 'Hari H';
    //             } elseif ($diff === -3) {
    //                 $label = 'H+3';
    //             }
    //
    //             if ($label) {
    //                 $msg = "Halo {$debt->customer->name}, ini pengingat tagihan Anda sebesar Rp ".
    //                        number_format($debt->amount).' yang jatuh tempo pada '.
    //                        $debt->due_date.'. Segera lakukan pembayaran. Terima kasih.';
    //
    //                 $waService->sendMessage($debt->customer->whatsapp_number, $msg, $debt->id);
    //
    //                 // Update status pengingat terakhir di DB [cite: 73]
    //                 $debt->update(['last_reminder_sent' => $label]);
    //             }
    //         }
    //     }
}
