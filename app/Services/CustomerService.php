<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function getAllByUser($userId)
    {
        return Customer::where('user_id', $userId)
            ->with(['debts' => function($query) {
                $query->select('id', 'customer_id', 'amount', 'status');
            }])
            ->latest()
            ->paginate(10);
    }

    public function createCustomer(array $data)
    {
        $customer = Customer::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'customer_flag' => $data['customer_flag'] ?? 'aman',
        ]);

        $this->logService->record(
            "Mendaftarkan pelanggan baru: {$customer->name}",
            'Customer',
            $customer->id
        );

        return $customer;
    }

    public function getCustomerCount($userId)
    {
        return Customer::where('user_id', $userId)->count();
    }
}
