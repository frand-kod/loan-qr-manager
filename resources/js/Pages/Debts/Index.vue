<script setup>
import { ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import AppLayout from "../../Layout/AppLayout.vue";
import DebtTable from "./Partials/DebtTable.vue";
import DebtModal from "./Partials/DebtModal.vue";
import QrisModal from "./Partials/QrisModal.vue";

const props = defineProps({
    debts: Object,
    customers: Array,
});

const isModalOpen = ref(false);
const selectedDebt = ref(null); // Menyimpan data hutang yang akan diedit

const page = usePage();
const qrisData = ref(null);
watch(
    () => page.props.flash?.qris_data,
    (newData) => {
        if (newData) {
            qrisData.value = newData;
            // Logic untuk membuka modal QRIS
            console.log("QRIS Ready:", newData);
        }
    },
    { deep: true }
);
const openAddModal = () => {
    selectedDebt.ref = null; // Reset untuk mode tambah
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
        <QrisModal :data="qrisData" @close="qrisData = null" />
    </AppLayout>
</template>
