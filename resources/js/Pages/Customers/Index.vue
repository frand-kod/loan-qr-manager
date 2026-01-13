<template>
    <AppLayout>
        <div class="space-y-6">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
                <div>
                    <h1
                        class="text-2xl font-medium text-slate-800 tracking-tight"
                    >
                        Data Pelanggan
                    </h1>
                    <p class="text-sm text-slate-500">
                        Kelola informasi dan kualitas kredit pelanggan Anda.
                    </p>
                </div>
                <button
                    @click="openModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-l font-medium shadow-lg shadow-blue-100 transition-all active:scale-95 text-sm"
                >
                    + Tambah Pelanggan
                </button>
            </div>

            <div class="relative max-w-md group">
                <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 group-focus-within:text-blue-500 transition-colors"
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
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </span>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari nama atau nomor WA..."
                    class="w-full pl-10 pr-4 py-3 bg-white border border-slate-100 rounded-l focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all text-sm shadow-sm"
                />
            </div>

            <div
                class="bg-white border border-slate-100 rounded-l shadow-sm overflow-hidden"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead
                            class="bg-slate-50/50 text-slate-500 text-[11px] uppercase tracking-widest font-medium"
                        >
                            <tr>
                                <th>No</th>
                                <th class="px-6 py-5">Nama Pelanggan</th>
                                <th class="px-6 py-5 text-center">
                                    No. WhatsApp
                                </th>
                                <th class="px-6 py-5 text-center">Status</th>
                                <th class="px-6 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tr
                            v-for="customer in customers.data"
                            :key="customer.id"
                            class="hover:bg-slate-50/80 transition-colors group"
                        >
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">
                                    {{}}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">
                                    {{ customer.name }}
                                </div>
                                <div
                                    class="text-[10px] text-slate-400 font-medium uppercase"
                                >
                                    DIBUAT:
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
                                    class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-l hover:bg-green-100 transition-colors text-xs font-semibold"
                                >
                                    {{ customer.whatsapp_number }}
                                    <svg
                                        class="w-3 h-3 ml-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                        />
                                    </svg>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    :class="
                                        getFlagStyle(customer.customer_flag)
                                    "
                                    >{{ customer.customer_flag }}</span
                                >
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="openModal(customer)"
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-l transition-all"
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
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 113.536 3.536L12 14.036H9v-3.572L16.732 3.732z"
                                        />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="customers.data.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-slate-400 text-sm italic mb-2">
                                    Data tidak ditemukan.
                                </p>
                                <button
                                    v-if="search"
                                    @click="search = ''"
                                    class="text-blue-600 text-xs font-bold uppercase tracking-tighter hover:underline"
                                >
                                    Clear Search
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>

                <div
                    v-if="customers.links.length > 3"
                    class="px-6 py-4 bg-slate-50/30 border-t border-slate-50 flex justify-between items-center"
                >
                    <div class="text-xs text-slate-500 font-medium">
                        Showing {{ customers.from }} to {{ customers.to }} of
                        {{ customers.total }} customers
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, k) in customers.links" :key="k">
                            <button
                                v-if="link.url"
                                @click="router.get(link.url)"
                                class="px-3 py-1 text-xs rounded-l border transition-all"
                                :class="
                                    link.active
                                        ? 'bg-blue-600 border-blue-600 text-white shadow-md'
                                        : 'bg-white border-slate-100 text-slate-600 hover:bg-slate-50'
                                "
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-[60] flex items-center justify-center p-4"
            >
                <div
                    class="absolute inset-0 bg-slate-900/10 backdrop-blur-[2px]"
                    @click="closeModal"
                ></div>
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                >
                    <div
                        class="relative bg-white w-full max-w-md rounded-l shadow-2xl border border-slate-100 overflow-hidden"
                    >
                        <div
                            class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30"
                        >
                            <h3 class="font-medium text-slate-800">
                                {{
                                    isEdit ? "Perbarui Data" : "Pelanggan Baru"
                                }}
                            </h3>
                            <button
                                @click="closeModal"
                                class="p-2 text-slate-400 hover:text-slate-800 rounded-full transition-colors"
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
                        <form
                            @submit.prevent="submitForm"
                            class="p-6 space-y-5"
                        >
                            <div>
                                <label
                                    class="block text-[10px] font-medium text-slate-400 uppercase tracking-widest mb-1.5"
                                    >Nama Lengkap</label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-l focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all text-sm"
                                    placeholder="Nama..."
                                    required
                                />
                                <p
                                    v-if="form.errors.name"
                                    class="text-[10px] text-red-500 mt-1 font-medium italic"
                                >
                                    {{ form.errors.name }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-medium text-slate-400 uppercase tracking-widest mb-1.5"
                                    >Nomor WhatsApp</label
                                >
                                <input
                                    v-model="form.whatsapp_number"
                                    type="text"
                                    inputmode="numeric"
                                    oninput="
                                    this.value = this.value
                                        .replace(/(?!^\+)[^\d]/g, '')
                                        .replace(/^(\+{2,})/, '+')
                                    "
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-l focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all text-sm"
                                    placeholder="08..."
                                    minlength="10"
                                    maxlength="15"
                                    required
                                />
                                <p
                                    v-if="form.errors.whatsapp_number"
                                    class="text-[10px] text-red-500 mt-1 font-medium italic"
                                >
                                    {{ form.errors.whatsapp_number }}
                                </p>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-medium text-slate-400 uppercase tracking-widest mb-1.5"
                                    >Status Pelanggan</label
                                >
                                <div class="relative">
                                    <select
                                        v-model="form.customer_flag"
                                        class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-l focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all text-sm appearance-none"
                                    >
                                        <option value="safe">
                                            Aman (Lancar)
                                        </option>
                                        <option value="warning">
                                            Warning (Telat-telat)
                                        </option>
                                        <option value="crash">
                                            Macet (Bermasalah)
                                        </option>
                                        <option value="blacklist">
                                            Blacklist
                                        </option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400"
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
                                                d="M19 9l-7 7-7-7"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="pt-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full bg-blue-600 text-white py-4 rounded-l font-medium shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all active:scale-[0.98] disabled:opacity-50 text-sm tracking-wide"
                                >
                                    {{
                                        isEdit
                                            ? "Simpan Perubahan"
                                            : "Daftarkan Customer"
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
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
        "px-3 py-1 rounded-l text-[10px] font-medium uppercase tracking-widest border ";
    switch (flag) {
        case "safe":
            return base + "bg-green-50 text-green-600 border-green-100";
        case "warning":
            return base + "bg-amber-50 text-amber-600 border-amber-100";
        case "crash":
            return base + "bg-red-50 text-red-600 border-red-100";
        case "blacklist":
            return base + "bg-slate-900 text-white border-slate-900";
        default:
            return base + "bg-slate-50 text-slate-500 border-slate-100";
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
