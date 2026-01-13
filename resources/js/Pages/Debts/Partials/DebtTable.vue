<script setup>
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

defineProps({ debts: Object });

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
const generateQRIS = (id) => {
    if (confirm("Generate kode QRIS untuk pembayaran ini?")) {
        router.post(
            route("debts.pay-qris", id),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Berhasil: Data qris_data akan dikirim Laravel ke flash props
                    console.log("Request QRIS berhasil dikirim");
                },
                onError: (err) => {
                    alert(
                        "Gagal generate QRIS. Cek log server atau konfigurasi Tripay."
                    );
                    console.error(err);
                },
            }
        );
    }
};
const confirmDelete = (id) => {
    // Tambahkan log untuk memastikan ID terbaca
    console.log("Menghapus ID:", id);

    if (window.confirm("Apakah Anda yakin ingin menghapus data ini?")) {
        router.delete(route("debts.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Berhasil dihapus");
            },
            onError: (errors) => {
                console.error("Gagal menghapus:", errors);
            },
        });
    }
};

// ... kode lainnya ...
const sendWA = (id) => {
    router.post(route("debts.send-reminder", id), {
        preserveScroll: true,
        onStart: () => {
            /* tampilkan loading jika perlu */
        },
    });
};

const getStatusClass = (status) => {
    const styles = {
        pending: "bg-yellow-50 text-yellow-700 border-yellow-100",
        partial: "bg-blue-50 text-blue-700 border-blue-100",
        paid: "bg-green-50 text-green-700 border-green-100",
        expired: "bg-red-50 text-red-700 border-red-100",
    };
    return `px-2 py-1 rounded-md text-[10px] font-bold uppercase border ${styles[status]}`;
};
</script>

<template>
    <div
        class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >
                            Ref ID
                        </th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                        >
                            Pelanggan
                        </th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right"
                        >
                            Nominal
                        </th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right"
                        >
                            Sisa
                        </th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center"
                        >
                            Jatuh Tempo
                        </th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center"
                        >
                            Status
                        </th>
                        <th
                            class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr
                        v-for="debt in debts.data"
                        :key="debt.id"
                        class="hover:bg-slate-50/50 transition-colors"
                    >
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-blue-600 mb-0.5">
                                {{ debt.reference_id }}
                            </div>
                        </td>
                        <td>
                            <div class="font-medium text-slate-700">
                                {{ debt.customer.name }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 text-right font-semibold text-slate-700"
                        >
                            {{ formatCurrency(debt.amount) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div
                                :class="
                                    debt.remaining_amount > 0
                                        ? 'text-orange-600'
                                        : 'text-slate-400'
                                "
                                class="font-bold"
                            >
                                {{ formatCurrency(debt.remaining_amount) }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 text-center text-xs text-slate-600"
                        >
                            {{ debt.due_date }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span :class="getStatusClass(debt.status)">{{
                                debt.status
                            }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button
                                @click="$emit('edit', debt)"
                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                title="Edit"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="confirmDelete(debt.id)"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                title="Hapus"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="sendWA(debt.id)"
                                class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all"
                                title="Kirim Tagihan WA"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    />
                                </svg>
                            </button>
                            <button
                                v-if="debt.status !== 'paid'"
                                @click="generateQRIS(debt.id)"
                                class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg"
                                title="Bayar via QRIS"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                    />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
