<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Debt;
use App\Services\PaymentService;
use App\Services\Web\DebtService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DebtController extends Controller
{
    protected $debtService;

    protected $paymentService;

    // 3. Masukkan kedua Service ke dalam constructor
    public function __construct(DebtService $debtService, PaymentService $paymentService)
    {
        $this->debtService = $debtService;
        $this->paymentService = $paymentService;
    }

    /**
     * Menampilkan daftar hutang
     */
    public function index(Request $request)
    {
        return Inertia::render('Debts/Index', [
            'debts' => $this->debtService->getDebts(10, $request->search),
            'customers' => Customer::where('user_id', auth()->id())->get(['id', 'name']), // Untuk pilihan di modal tambah
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menyimpan data hutang baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:500',
            'due_date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:255',
        ], [
            'customer_id.required' => 'Pilih pelanggan dulu, ya.',
            'amount.min' => 'Minimal nominal hutang adalah Rp 500.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo tidak boleh masa lalu.',
        ]);

        $this->debtService->createDebt($validated);

        return redirect()->back()->with('success', 'Hutang baru berhasil dicatat!');
    }

    /**
     * Menghapus data hutang
     */
    public function destroy($id)
    {
        $debt = Debt::where('user_id', auth()->id())->findOrFail($id);
        $debt->delete();

        return redirect()->back()->with('success', 'Data hutang berhasil dihapus.');
    }

    /**
     * Update status jika ingin diubah manual (Opsional)
     */
    public function updateStatus(Request $request, $id)
    {
        $debt = Debt::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,partial,paid,expired',
        ]);

        $debt->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status hutang diperbarui.');
    }

    public function update(Request $request, $id)
    {
        $debt = Debt::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:500',
            'due_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        // Update nominal sisa juga jika nominal awal diubah (logika sederhana)
        // Hati-hati: Jika sudah ada cicilan, logika ini perlu disesuaikan
        $validated['remaining_amount'] = $request->amount;

        $debt->update($validated);

        return redirect()->back()->with('success', 'Data hutang berhasil diperbarui.');
    }

    public function sendReminder($id)
    {
        $success = $this->debtService->sendManualReminder($id);

        if ($success) {
            return back()->with('success', 'Reminder WhatsApp berhasil dikirim.');
        }

        return back()->with('error', 'Gagal mengirim WhatsApp. Cek koneksi API Anda.');
    }

    public function testWhatsapp(Request $request)
    {
        $request->validate(['phone' => 'required']);

        $waService = app(\App\Services\WhatsappService::class);
        $test = $waService->sendMessage($request->phone, 'Test Koneksi Sistem Hutang: Koneksi Berhasil! ✅');

        if ($test) {
            return back()->with('success', 'Pesan test berhasil dikirim.');
        }

        return back()->with('error', 'Koneksi API WhatsApp gagal.');
    }

    public function markAsPaid(Debt $debt)
    {
        // Pastikan milik user yang login
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        // Proses pelunasan melalui Service
        $this->paymentService->handleManualPayment($debt);

        return back()->with('success', 'Hutang telah dilunasi secara manual.');
    }
}
