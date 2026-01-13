<script setup>
const props = defineProps({
    data: Object, // Menampung objek qris_data dari Tripay
});

const emit = defineEmits(['close']);

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <div v-if="data" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full text-center shadow-2xl border border-slate-100 relative">
            
            <div class="flex justify-between items-center mb-6">
                <div class="text-left">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Metode Pembayaran</p>
                    <h3 class="text-sm font-bold text-slate-800">QRIS (Otomatis)</h3>
                </div>
                <button @click="$emit('close')" class="text-slate-400 hover:text-slate-600 bg-slate-50 w-8 h-8 rounded-full flex items-center justify-center transition-all">
                    &times;
                </button>
            </div>

            <div class="bg-white p-3 border border-slate-100 rounded-2xl shadow-inner mb-6 flex justify-center">
                <img :src="data.qr_url" alt="QRIS" class="w-64 h-64 rounded-xl" />
            </div>

            <div class="mb-8 p-4 bg-blue-50/50 rounded-2xl">
                <p class="text-xs text-blue-600 font-medium mb-1">Total Tagihan</p>
                <h2 class="text-3xl font-black text-slate-900">
                    {{ formatCurrency(data.amount) }}
                </h2>
                <p class="text-[10px] text-slate-500 mt-2 italic font-medium">
                    Ref: {{ data.merchant_ref }}
                </p>
            </div>

            <div class="space-y-3">
                <button 
                    @click="$emit('close')" 
                    class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200"
                >
                    Saya Sudah Bayar
                </button>
                <p class="text-[10px] text-slate-400 font-medium">
                    Silakan screenshot halaman ini untuk pembayaran via galeri.
                </p>
            </div>
        </div>
    </div>
</template>