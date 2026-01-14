<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    show: Boolean,
    qrisData: Object,
});

const emit = defineEmits(["close"]);
const isChecking = ref(false);
let pollInterval = null;

// TAMBAHKAN KEMBALI FUNGSI INI
const formatCurrency = (value) => {
    if (!value) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const checkPayment = (isSilent = false) => {
    if (!isSilent) isChecking.value = true;

    router.post(
        route("debts.check-payment", props.qrisData.debt_id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isChecking.value = false;
            },
            onSuccess: (page) => {
                if (page.props.flash.success) {
                    emit("close");
                }
            },
        }
    );
};

onMounted(() => {
    pollInterval = setInterval(() => {
        if (props.show && props.qrisData) {
            checkPayment(true);
        }
    }, 7000);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>

<template>
    <div
        v-if="show && qrisData"
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/40 backdrop-blur-md"
    >
        <div class="absolute inset-0" @click="$emit('close')"></div>

        <div
            class="relative bg-white rounded-[1rem] max-w-[360px] w-full shadow-2xl border border-white overflow-hidden animate-in fade-in zoom-in duration-300"
        >
            <div
                class="px-8 pt-8 pb-4 flex justify-between items-start text-slate-900"
            >
                <div>
                    <h3 class="text-base font-semibold tracking-tight">
                        Pembayaran QRIS
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5 font-normal">
                        Scan melalui e-wallet atau m-banking
                    </p>
                </div>
                <button
                    @click="$emit('close')"
                    class="text-slate-400 hover:text-slate-900 transition-colors"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <div class="px-8 py-4 text-center">
                <div
                    class="bg-slate-50 p-5 rounded-2xl border border-slate-100 flex justify-center mb-6"
                >
                    <div
                        class="bg-white p-2 rounded-lg shadow-sm border border-slate-50"
                    >
                        <img
                            :src="qrisData.qr_url"
                            alt="QRIS"
                            class="w-48 h-48 object-contain"
                        />
                    </div>
                </div>

                <div class="mb-2">
                    <span
                        class="text-[9px] font-bold text-blue-600 uppercase tracking-widest mb-1 block"
                    >
                        Total Pembayaran
                    </span>
                    <h2
                        class="text-2xl font-bold text-slate-900 tracking-tighter"
                    >
                        {{ formatCurrency(qrisData.amount) }}
                    </h2>
                </div>

                <div class="flex items-center justify-center gap-1.5 mt-2">
                    <div
                        class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"
                    ></div>
                    <span
                        class="text-[9px] font-medium text-slate-400 uppercase"
                    >
                        ID: {{ qrisData.merchant_ref }}
                    </span>
                </div>
            </div>

            <div class="p-8 pt-4">
                <button
                    @click="checkPayment(false)"
                    :disabled="isChecking"
                    class="w-full py-3.5 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-all active:scale-[0.98] disabled:opacity-50"
                >
                    {{ isChecking ? "Mengecek..." : "Konfirmasi Pembayaran" }}
                </button>
                <p
                    class="text-[9px] text-center text-slate-400 mt-4 font-normal leading-relaxed"
                >
                    Sistem mengecek pembayaran secara otomatis setiap 7
                    detik.<br />
                    Klik tombol jika Anda sudah menyelesaikan transaksi.
                </p>
            </div>

            <div
                class="bg-slate-50/50 py-3 border-t border-slate-100 flex justify-center grayscale opacity-30"
            >
                <img
                    src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg"
                    class="h-3.5"
                    alt="QRIS Logo"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
div {
    font-family: "Inter", -apple-system, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>
