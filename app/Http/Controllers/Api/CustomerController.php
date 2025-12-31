<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAllByUser(auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Daftar nasabah berhasil diambil',
            'data' => $customers,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|unique:customers,whatsapp_number',
            'customer_flag' => 'nullable|string|in:aman,waspada,blacklisted',
        ]);

        // check validasi
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // lakukan proses penyimpanan data
        try {
            $customer = $this->customerService->createCustomer($request->all());

            return response()->json([
                'success' => 'true',
                'message' => 'Customer '.$customer->name.' berhasil di tambahkan',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 'false',
                'message' => 'Customer '.$customer->name.' berhasil di tambahkan',
            ], 500);
        }
    }
}
