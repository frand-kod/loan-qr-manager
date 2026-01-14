<script setup>
import { Link, router } from "@inertiajs/vue3";

defineProps({ debts: Object });

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const sendReminder = (id) => {
    if (confirm("Kirim pengingat WhatsApp ke pelanggan?")) {
        router.post(
            route("debts.send-whatsapp", id),
            {},
            {
                preserveScroll: true,
            }
        );
    }
};

const getStatusStyles = (status) => {
    const styles = {
        pending: "bg-amber-50 text-amber-600 border-amber-100",
        partial: "bg-sky-50 text-sky-600 border-sky-100",
        paid: "bg-emerald-50 text-emerald-600 border-emerald-100",
        expired: "bg-rose-50 text-rose-600 border-rose-100",
    };
    return styles[status] || "bg-slate-50 text-slate-600 border-slate-100";
};

const generateQRIS = (id) => {
    router.post(
        route("debts.pay-qris", id),
        {},
        {
            preserveScroll: true,
            onBefore: () => {
                // Anda bisa menggunakan event bus atau memicu loading di sini
                // Namun, Inertia memiliki properti 'processing' jika Anda memakai useForm
            },
            onStart: () => {
                // Jika Anda ingin menggunakan state di Index.vue,
                // lebih baik handle loading via global event di Index.vue
            },
        }
    );
};

const confirmManualPayment = (debt) => {
    if (confirm(`Konfirmasi pelunasan manual untuk ${debt.customer.name}?`)) {
        router.post(
            route("debts.mark-as-paid", debt.id),
            {},
            { preserveScroll: true }
        );
    }
};

const confirmDelete = (id) => {
    if (confirm("Hapus data piutang ini?")) {
        router.delete(route("debts.destroy", id), { preserveScroll: true });
    }
};
</script>

<template>
    <div
        class="bg-white border border-slate-200 overflow-hidden shadow-sm"
        style="border-radius: 4px"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse antialiased">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200">
                        <th
                            class="pl-6 pr-3 py-4 text-[10px] font-semibold text-slate-400 uppercase tracking-[2px]"
                        >
                            Info Transaksi
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-semibold text-slate-400 uppercase tracking-[2px]"
                        >
                            Pelanggan
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-semibold text-slate-400 uppercase tracking-[2px] text-right"
                        >
                            Nominal
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-semibold text-slate-400 uppercase tracking-[2px] text-right"
                        >
                            Sisa
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-semibold text-slate-400 uppercase tracking-[2px] text-center"
                        >
                            Tempo
                        </th>
                        <th
                            class="px-4 py-4 text-[10px] font-semibold text-slate-400 uppercase tracking-[2px] text-center"
                        >
                            Status
                        </th>
                        <th
                            class="pl-3 pr-6 py-4 text-[10px] font-semibold text-slate-400 uppercase tracking-[2px] text-right"
                        >
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="debt in debts.data"
                        :key="debt.id"
                        class="hover:bg-slate-50/50 transition-colors group"
                    >
                        <td class="pl-6 pr-3 py-4">
                            <span
                                class="text-[11px] font-mono text-blue-600 bg-blue-50 px-2 py-0.5 border border-blue-100"
                                style="border-radius: 2px"
                            >
                                #{{ debt.reference_id }}
                            </span>
                        </td>

                        <td class="px-4 py-4">
                            <div
                                class="text-sm font-normal text-slate-700 tracking-tight"
                            >
                                {{ debt.customer.name }}
                            </div>
                        </td>

                        <td class="px-4 py-4 text-right">
                            <span class="text-sm font-light text-slate-500">{{
                                formatCurrency(debt.amount)
                            }}</span>
                        </td>

                        <td class="px-4 py-4 text-right">
                            <span
                                class="text-sm font-medium"
                                :class="
                                    debt.remaining_amount > 0
                                        ? 'text-slate-900'
                                        : 'text-slate-400'
                                "
                            >
                                {{ formatCurrency(debt.remaining_amount) }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-center">
                            <span
                                class="text-[12px] font-normal text-slate-600"
                                >{{ debt.due_date }}</span
                            >
                        </td>

                        <td class="px-4 py-4 text-center">
                            <span
                                :class="getStatusStyles(debt.status)"
                                class="inline-block px-2 py-0.5 text-[9px] font-semibold uppercase tracking-wider border"
                                style="border-radius: 2px"
                            >
                                {{ debt.status }}
                            </span>
                        </td>

                        <td class="pl-3 pr-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <div
                                    v-if="debt.status !== 'paid'"
                                    class="flex border border-slate-200 divide-x divide-slate-200"
                                    style="border-radius: 2px"
                                >
                                    <button
                                        @click="generateQRIS(debt.id)"
                                        class="p-1.5 text-slate-600 hover:bg-slate-50 transition-all"
                                        title="QRIS"
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
                                                stroke-width="1.5"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="confirmManualPayment(debt)"
                                        class="p-1.5 text-slate-600 hover:bg-slate-50 transition-all"
                                        title="Lunas Manual"
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
                                                stroke-width="1.5"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="sendReminder(debt.id)"
                                        class="p-1.5 text-emerald-600 hover:bg-emerald-50 transition-all"
                                        title="WhatsApp Reminder"
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
                                                stroke-width="1.5"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <button
                                    @click="confirmDelete(debt.id)"
                                    class="p-2 text-slate-300 hover:text-rose-600 transition-colors"
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
                                            stroke-width="1.5"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
/* Force Thin Look */
table {
    font-family: "Inter", -apple-system, sans-serif;
    letter-spacing: -0.01em;
}
</style>
