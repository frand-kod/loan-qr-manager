<template>
    <AppLayout>
        <div class="space-y-6">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-6"
            >
                <div>
                    <h1
                        class="text-xl font-normal text-slate-800 tracking-tight"
                    >
                        Data Pelanggan
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Kelola informasi dan kualitas kredit pelanggan Anda.
                    </p>
                </div>
                <button
                    @click="openModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-none font-normal transition-all active:scale-95 text-sm tracking-wide"
                >
                    + Tambah Pelanggan
                </button>
            </div>

            <div class="relative max-w-sm group">
                <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400"
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
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama atau nomor..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-none focus:border-blue-500 outline-none transition-all text-sm font-normal shadow-sm"
                />
            </div>

            <div
                class="bg-white border border-slate-200 rounded-none shadow-sm overflow-hidden"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-[0.15em] font-semibold border-b border-slate-200"
                            >
                                <th class="px-6 py-4 w-16 text-center">No</th>
                                <th class="px-6 py-4">Nama Pelanggan</th>
                                <th class="px-6 py-4 text-center">WhatsApp</th>
                                <th class="px-6 py-4 text-center">
                                    Kualitas Kredit
                                </th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="(customer, index) in customers.data"
                                :key="customer.id"
                                class="odd:bg-white even:bg-slate-50/50 hover:bg-blue-50/30 transition-colors"
                            >
                                <td
                                    class="px-6 py-4 text-center text-sm text-slate-400 font-normal"
                                >
                                    {{ customers.from + index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="text-sm font-normal text-slate-700"
                                    >
                                        {{ customer.name }}
                                    </div>
                                    <div
                                        class="text-[10px] text-slate-400 font-normal mt-0.5 uppercase tracking-tighter"
                                    >
                                        Terdaftar:
                                        {{ formatDate(customer.created_at) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a
                                        :href="
                                            'https://wa.me/' +
                                            customer.whatsapp_number
                                        "
                                        target="_blank"
                                        class="text-sm font-normal text-blue-600 hover:text-blue-800 border-b border-transparent hover:border-blue-800 pb-0.5 transition-all"
                                    >
                                        {{ customer.whatsapp_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        :class="
                                            getFlagStyle(customer.customer_flag)
                                        "
                                    >
                                        {{ customer.customer_flag }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        @click="openModal(customer)"
                                        class="text-sm font-normal text-slate-500 hover:text-blue-600 transition-colors uppercase tracking-widest px-2 py-1"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="customers.links.length > 3"
                    class="px-6 py-4 bg-white border-t border-slate-100 flex justify-between items-center"
                >
                    <span
                        class="text-[11px] text-slate-400 font-normal uppercase tracking-wider"
                    >
                        Hal {{ customers.current_page }} dari
                        {{ customers.last_page }}
                    </span>
                    <div class="flex gap-0 border border-slate-200 shadow-sm">
                        <template v-for="(link, k) in customers.links" :key="k">
                            <button
                                v-if="link.url"
                                @click="router.get(link.url)"
                                class="px-3 py-1.5 text-[11px] font-normal transition-all border-r last:border-r-0"
                                :class="
                                    link.active
                                        ? 'bg-slate-800 text-white border-slate-800'
                                        : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'
                                "
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <Transition name="fade">
            <div
                v-if="showModal"
                class="fixed inset-0 z-[60] flex items-center justify-center p-4"
            >
                <div
                    class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                    @click="closeModal"
                ></div>
                <div
                    class="relative bg-white w-full max-w-sm rounded-none shadow-2xl overflow-hidden border border-slate-200"
                >
                    <div
                        class="p-5 border-b border-slate-100 flex justify-between items-center"
                    >
                        <h3
                            class="text-sm font-normal text-slate-800 uppercase tracking-widest"
                        >
                            {{ isEdit ? "Update Pelanggan" : "Pelanggan Baru" }}
                        </h3>
                    </div>
                    <form @submit.prevent="submitForm" class="p-6 space-y-4">
                        <div>
                            <label
                                class="block text-[10px] font-normal text-slate-400 uppercase tracking-widest mb-1.5"
                                >Nama Lengkap</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-3 py-2 border border-slate-200 rounded-none focus:border-blue-500 outline-none text-sm font-normal"
                                required
                            />
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-normal text-slate-400 uppercase tracking-widest mb-1.5"
                                >WhatsApp</label
                            >
                            <input
                                v-model="form.whatsapp_number"
                                type="text"
                                class="w-full px-3 py-2 border border-slate-200 rounded-none focus:border-blue-500 outline-none text-sm font-normal"
                                required
                            />
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-normal text-slate-400 uppercase tracking-widest mb-1.5"
                                >Status Flag</label
                            >
                            <select
                                v-model="form.customer_flag"
                                class="w-full px-3 py-2 border border-slate-200 rounded-none outline-none text-sm font-normal bg-white"
                            >
                                <option value="safe text-sm">SAFE</option>
                                <option value="warning text-sm">WARNING</option>
                                <option value="crash text-sm">CRASH</option>
                                <option value="blacklist text-sm">
                                    BLACKLIST
                                </option>
                            </select>
                        </div>
                        <div class="pt-4">
                            <button
                                type="submit"
                                class="w-full bg-slate-900 text-white py-3 rounded-none text-sm font-normal uppercase tracking-[0.2em] hover:bg-slate-800 transition-all"
                            >
                                {{
                                    isEdit ? "Simpan Perubahan" : "Simpan Data"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import AppLayout from "../../Layout/AppLayout.vue";

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const showModal = ref(false);
const isEdit = ref(false);
const editId = ref(null);

const form = useForm({
    name: "",
    whatsapp_number: "",
    customer_flag: "safe",
});

let timeout = null;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            "/customers",
            { search: value },
            { preserveState: true, replace: true, preserveScroll: true }
        );
    }, 500);
});

const openModal = (customer = null) => {
    if (customer) {
        isEdit.value = true;
        editId.value = customer.id;
        form.name = customer.name;
        form.whatsapp_number = customer.whatsapp_number;
        form.customer_flag = customer.customer_flag;
    } else {
        isEdit.value = false;
        editId.value = null;
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submitForm = () => {
    const url = isEdit.value ? `/customers/${editId.value}` : "/customers";
    const method = isEdit.value ? "put" : "post";

    form[method](url, {
        onSuccess: () => closeModal(),
    });
};

const getFlagStyle = (flag) => {
    const base =
        "px-2 py-0.5 rounded-none text-[9px] font-normal uppercase tracking-wider border ";
    switch (flag) {
        case "safe":
            return base + "bg-white text-green-600 border-green-200";
        case "warning":
            return base + "bg-white text-amber-600 border-amber-200";
        case "crash":
            return base + "bg-white text-red-600 border-red-200";
        case "blacklist":
            return base + "bg-slate-900 text-white border-slate-900";
        default:
            return base + "bg-white text-slate-400 border-slate-200";
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};
</script>

<style scoped>
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
::-webkit-scrollbar-track {
    background: transparent;
}
</style>
