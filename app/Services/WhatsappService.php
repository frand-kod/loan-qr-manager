<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function sendMessage($to, $message, $debtId = null)
    {
        // Format URL: ?to=[number]&msg=[text]&secret=[key]
        $url = config('services.whatsapp.url');
        $secret = config('services.whatsapp.secret');

        $response = Http::withOptions([
            'verify' => false, // Mematikan pengecekan SSL
        ])->get($url, [
            'to' => $to,
            'msg' => $message,
            'secret' => $secret,
        ]);

        if ($response->successfull()) {
            $this->logService->record(
                "Pesann ke $to: ".substr($message, 0, 50).'...',
                'Debt',
                $debtId
            );

            return true;
        }

        return false;
    }
}
