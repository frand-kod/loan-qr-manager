<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Web\CustomerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $customers = $this->customerService->getPaginatedCustomers(
            $request->input('search')
        );

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:16|min:9|starts_with:08,+628', // Sesuaikan dengan database
            'customer_flag' => 'required|in:safe,warning,crash,blacklist',
        ]);

        $this->customerService->createCustomer($validated);

        return redirect()->back()->with('success', 'Customer berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'customer_flag' => 'required|in:safe,warning,crash,blacklist',
        ]);

        $this->customerService->updateCustomer($id, $validated);

        return redirect()->back()->with('success', 'Customer berhasil diperbarui.');
    }
}
