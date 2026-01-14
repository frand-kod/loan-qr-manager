<script setup>
import { ref, computed, watch, onUnmounted } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import QrisModal from "../Debts/Partials/QrisModal.vue";

const props = defineProps({
    debt: Object,
});

const page = usePage();
const showQris = ref(false);
const isGenerating = ref(false);
const currentQrisData = ref(null);
let pollingInterval = null;

// Watcher untuk QRIS Data
watch(
    () => page.props.flash?.qris_data,
    (newData) => {
        if (newData) {
            currentQrisData.value = newData;
            showQris.value = true;
            startPolling(); // Mulai cek status otomatis saat modal buka
        }
    },
    { immediate: true }
);

const startPolling = () => {
    if (pollingInterval) clearInterval(pollingInterval);
    pollingInterval = setInterval(() => {
        router.post(
            route("debts.check-payment", props.debt.id),
            {},
            {
                preserveScroll: true,
                only: ["debt", "flash"], // Hanya ambil data yang diperlukan
                onSuccess: (page) => {
                    if (props.debt.status === "paid") {
                        clearInterval(pollingInterval);
                        showQris.value = false;
                    }
                },
                onError: () => clearInterval(pollingInterval),
            }
        );
    }, 5000);
};

const payNow = () => {
    router.post(
        route("debts.pay-qris", props.debt.id),
        {},
        {
            onBefore: () => (isGenerating.value = true),
            onFinish: () => (isGenerating.value = false),
        }
    );
};

onUnmounted(() => {
    if (pollingInterval) clearInterval(pollingInterval);
});
</script>

<template>
    <div
        class="min-h-screen bg-[#f3f4f7] flex flex-col items-center py-12 px-4 font-sans"
    >
        <div class="mb-8">
            <h2
                class="text-2xl font-bold text-slate-800 flex items-center gap-2"
            >
                Piutangku
            </h2>
        </div>

        <div
            class="w-full max-w-md bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden"
        >
            <div class="p-6 border-b border-slate-100 flex items-center gap-4">
                <div
                    class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 font-bold text-xl"
                >
                    {{ debt.customer.name.charAt(0) }}
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 leading-none mb-1">
                        Pembayaran Piutang
                    </h3>
                    <p
                        class="text-xs text-slate-500 uppercase tracking-wider font-medium"
                    >
                        {{ debt.reference_id }}
                    </p>
                </div>
            </div>

            <div class="p-6 bg-slate-50/50">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm text-slate-600">Total Pembayaran</span>
                    <span
                        class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded"
                        >Detail</span
                    >
                </div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Rp
                    {{ Number(debt.remaining_amount).toLocaleString("id-ID") }}
                </h2>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Nama Pelanggan</span>
                    <span class="text-slate-800 font-medium">{{
                        debt.customer.name
                    }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Metode Pembayaran</span>
                    <span class="text-slate-400 italic"
                        >Pilih setelah klik bayar</span
                    >
                </div>

                <hr class="border-slate-100" />

                <button
                    @click="payNow"
                    :disabled="isGenerating || debt.status === 'paid'"
                    class="w-full bg-[#1a73e8] hover:bg-blue-700 disabled:bg-slate-300 text-white font-semibold py-3.5 rounded-lg transition-all shadow-md active:scale-[0.98]"
                >
                    <div
                        v-if="isGenerating"
                        class="flex items-center justify-center gap-3"
                    >
                        <div
                            class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"
                        ></div>
                        <span>Memproses...</span>
                    </div>
                    <span v-else>{{
                        debt.status === "paid"
                            ? "Sudah Terbayar"
                            : "Bayar Sekarang"
                    }}</span>
                </button>
            </div>

            <div
                class="px-6 py-4 bg-white border-t border-slate-50 flex items-center justify-center gap-2"
            >
                <svg
                    class="w-4 h-4 text-green-500"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        fill-rule="evenodd"
                        d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 4.946-2.597 9.29-6.505 11.729l-.495.306-.495-.306C6.597 16.29 4 11.946 4 7.001c0-.68.056-1.35.166-2.002zm7.834 1.334a1 1 0 00-1 1v3a1 1 0 002 0v-3a1 1 0 00-1-1zm0 7a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"
                    />
                </svg>
                <span
                    class="text-[11px] text-slate-400 font-medium uppercase tracking-widest"
                    >Secure Checkout by Tripay</span
                >
            </div>
        </div>

        <p class="mt-8 text-sm text-slate-500">
            Butuh bantuan?
            <a href="#" class="text-blue-600 font-medium">Hubungi Merchant</a>
        </p>

        <QrisModal
            :show="showQris"
            :qrisData="currentQrisData"
            @close="showQris = false"
        />
    </div>
</template>
