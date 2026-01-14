<script setup>
import { ref, watch, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AppLayout from "../../Layout/AppLayout.vue";
import DebtTable from "./Partials/DebtTable.vue";
import DebtModal from "./Partials/DebtModal.vue";
import QrisModal from "./Partials/QrisModal.vue";

router.on("start", (event) => {
    // Cek apakah request ditujukan ke fungsi pay-qris
    if (event.detail.visit.url.pathname.includes("pay-qris")) {
        isGeneratingQris.value = true;
    }
});

router.on("finish", () => {
    isGeneratingQris.value = false;
});

const props = defineProps({
    debts: Object,
    customers: Array,
});
const isGeneratingQris = ref(false);
const isModalOpen = ref(false);
const selectedDebt = ref(null); // Menyimpan data hutang yang akan diedit

const page = usePage();
const showQris = ref(false);
const currentQrisData = ref(null);

// Gunakan computed agar selalu sync dengan props terbaru
const flashQrisData = computed(() => page.props.flash.qris_data);

watch(
    flashQrisData,
    (newData) => {
        console.log("Data Flash Baru Diterima:", newData); // Cek apakah ini muncul di console
        if (newData) {
            currentQrisData.value = newData;
            showQris.value = true;
        }
    },
    { immediate: true, deep: true }
);

const closeQris = () => {
    showQris.value = false;
    // Bersihkan data qris agar tidak muncul lagi saat refresh
    currentQrisData.value = null;
};
const openAddModal = () => {
    selectedDebt.value = null; // Reset untuk mode tambah
    isModalOpen.value = true;
};

const openEditModal = (debt) => {
    selectedDebt.value = debt; // Isi data untuk mode edit
    isModalOpen.value = true;
};
</script>

<template>
    <AppLayout>
        <div
            class="flex md:flex-row items-center justify-between md:items-center justify-between gap-4"
        >
            <div>
                <h1 class="text-2xl font-medium text-slate-800 tracking-tight">
                    Data Piutang
                </h1>
                <p class="text-sm text-slate-500 mt-2">
                    Kelola informasi Piutang Anda
                </p>
            </div>
            <button
                @click="openAddModal"
                class="bg-blue-600 text-white px-6 py-3 rounded"
            >
                Tambah
            </button>
        </div>

        <DebtTable :debts="debts" @edit="openEditModal" />

        <DebtModal
            :show="isModalOpen"
            :customers="customers"
            :debt="selectedDebt"
            @close="(isModalOpen = false), (selectedDebt = null)"
        />
        <QrisModal
            :show="showQris"
            :qrisData="currentQrisData"
            @close="closeQris"
        />
        <div
            v-if="isGeneratingQris"
            class="fixed inset-0 z-[200] flex flex-col items-center justify-center bg-slate-900/20 backdrop-blur-sm transition-all"
        >
            <div
                class="bg-white p-6 rounded-2xl shadow-xl flex flex-col items-center"
            >
                <svg
                    class="animate-spin h-8 w-8 text-blue-600 mb-3"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
                <p class="text-sm font-medium text-slate-700">
                    Menyiapkan QRIS...
                </p>
                <p class="text-[10px] text-slate-400 mt-1">
                    Mohon tunggu sebentar
                </p>
            </div>
        </div>
    </AppLayout>
</template>
