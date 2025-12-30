<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer->name,
            'amount' => (int) $this->amount,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'payment_link' => url("/api/pay/{$this->reference_id}"), // BE menyediakan link
            'formatted_amount' => 'Rp '.number_format($this->amount, 0, ',', '.'),
        ];
    }
}
