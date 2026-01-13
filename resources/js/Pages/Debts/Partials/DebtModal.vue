<script setup>
import { useForm } from "@inertiajs/vue3";
import { watch } from "vue";

const props = defineProps({
    show: Boolean,
    customers: Array,
    debt: Object, // Data hutang yang akan diedit (null jika tambah baru)
});

const emit = defineEmits(["close"]);

const form = useForm({
    id: null,
    customer_id: "",
    amount: "",
    due_date: "",
    description: "",
});

// Perhatikan perubahan pada props.debt (saat tombol edit diklik)
watch(
    () => props.debt,
    (newVal) => {
        if (newVal) {
            form.id = newVal.id;
            form.customer_id = newVal.customer_id;
            form.amount = newVal.amount;
            form.due_date = newVal.due_date;
            form.description = newVal.description;
        } else {
            form.reset();
        }
    }
);

const submit = () => {
    if (form.id) {
        // Mode Edit
        form.put(route("debts.update", form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        // Mode Tambah
        form.post(route("debts.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    form.reset();
    emit("close");
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    >
        <div
            class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden relative"
        >
            <div
                class="p-6 border-b border-slate-100 flex justify-between items-center"
            >
                <h3 class="text-lg font-bold text-slate-800">
                    {{ form.id ? "Edit Data Hutang" : "Catat Hutang Baru" }}
                </h3>
                <button
                    @click="closeModal"
                    class="text-slate-400 hover:text-slate-600 text-2xl"
                >
                    &times;
                </button>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-4">
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-500 uppercase mb-1"
                        >Pilih Pelanggan</label
                    >
                    <select
                        v-model="form.customer_id"
                        :disabled="form.id"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 outline-none text-sm disabled:opacity-60"
                    >
                        <option value="">-- Pilih --</option>
                        <option
                            v-for="c in customers"
                            :key="c.id"
                            :value="c.id"
                        >
                            {{ c.name }}
                        </option>
                    </select>
                    <div
                        v-if="form.errors.customer_id"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.customer_id }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-500 uppercase mb-1"
                            >Nominal (Rp)</label
                        >
                        <input
                            v-model="form.amount"
                            type="number"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm"
                            placeholder="0"
                        />
                        <div
                            v-if="form.errors.amount"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.amount }}
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-500 uppercase mb-1"
                            >Jatuh Tempo</label
                        >
                        <input
                            v-model="form.due_date"
                            type="date"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm"
                        />
                        <div
                            v-if="form.errors.due_date"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.due_date }}
                        </div>
                    </div>
                </div>

                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-500 uppercase mb-1"
                        >Keterangan</label
                    >
                    <textarea
                        v-model="form.description"
                        rows="2"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm"
                        placeholder="Contoh: Pinjaman modal atau cicilan barang"
                    ></textarea>
                </div>

                <div class="flex flex-col-reverse md:flex-row gap-3 mt-6">
                    <button
                        type="button"
                        @click="closeModal"
                        class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-lg font-bold hover:bg-slate-200 transition-all"
                    >
                        Batal
                    </button>
                    <button
                        :disabled="form.processing"
                        class="flex-[2] py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200"
                    >
                        {{
                            form.processing
                                ? "Menyimpan..."
                                : form.id
                                ? "Perbarui Data"
                                : "Simpan Hutang"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
