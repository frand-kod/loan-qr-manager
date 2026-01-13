<?php

namespace App\Services\Web;

use App\Models\Customer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    /**
     * Mengambil daftar customer dengan pencarian dan pagination.
     */
    public function getPaginatedCustomers($search = null, $perPage = 10): LengthAwarePaginator
    {
        return Customer::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('whatsapp_number', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Menyimpan data customer baru.
     */
    public function createCustomer(array $data): Customer
    {
        // Bersihkan nomor WA (Contoh: "0812-3456" jadi "628123456")
        $data['whatsapp_number'] = $this->formatWhatsApp($data['whatsapp_number']);
        $data['user_id'] = Auth::id();

        return Customer::create($data);
    }

    public function updateCustomer($id, array $data)
    {
        $customer = Customer::where('user_id', auth()->id())->findOrFail($id);

        // Gunakan data_get atau check isset agar tidak error jika key tidak ada
        if (isset($data['whatsapp_number'])) {
            $data['whatsapp_number'] = $this->formatWhatsApp($data['whatsapp_number']);
        }

        $customer->update($data);

        return $customer;
    }

    private function formatWhatsApp($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62'.substr($number, 1);
        }

        return $number;
    }
}
