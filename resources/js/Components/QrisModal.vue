<script setup>
const props = defineProps({
    show: Boolean,
    qrisData: Object, // Berisi data dari Tripay (qr_url, nominal, dll)
});

const emit = defineEmits(["close"]);

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"
            @click="$emit('close')"
        ></div>

        <div
            v-if="qrisData"
            class="relative bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden animate-in fade-in zoom-in duration-300"
        >
            <div class="p-6 text-center">
                <img :src="qrisData.qr_url" class="mx-auto w-64 h-64 mb-4" />
                <p class="font-bold text-xl">{{ qrisData.amount }}</p>
                <button
                    @click="$emit('close')"
                    class="mt-4 bg-slate-200 px-4 py-2 rounded"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
