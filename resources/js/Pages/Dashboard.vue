<template>
    <AppLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-1">
                <h1
                    class="text-2xl font-semibold text-slate-800 tracking-tight"
                >
                    Dashboard
                </h1>
                <p class="text-sm text-slate-500 font-normal">
                    Welcome back! Here's your overview.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <StatCard
                    label="Total Customers"
                    :value="stats.total_customers?.toString() || '0'"
                    colorClass="bg-white border-l-4 border-l-blue-500 text-slate-700 shadow-sm"
                    class="rounded-sm border border-slate-100"
                />
                <StatCard
                    label="Total Paid"
                    :value="formatCurrency(stats.total_paid)"
                    colorClass="bg-white border-l-4 border-l-green-500 text-slate-700 shadow-sm"
                    class="rounded-sm border border-slate-100"
                />
                <StatCard
                    label="Pending Amount"
                    :value="formatCurrency(stats.total_pending)"
                    colorClass="bg-white border-l-4 border-l-yellow-500 text-slate-700 shadow-sm"
                    class="rounded-sm border border-slate-100"
                />
                <StatCard
                    label="Overdue Amount"
                    :value="formatCurrency(stats.total_overdue)"
                    colorClass="bg-white border-l-4 border-l-red-500 text-slate-700 shadow-sm"
                    class="rounded-sm border border-slate-100"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div
                    class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden"
                >
                    <div
                        class="px-6 py-4 border-b border-slate-50 bg-slate-50/30"
                    >
                        <h3
                            class="text-xs font-bold text-slate-600 uppercase tracking-widest flex items-center gap-2"
                        >
                            <span
                                class="w-2 h-2 rounded-full bg-blue-500"
                            ></span>
                            Top Debtors
                        </h3>
                    </div>

                    <div
                        v-if="stats.top_customers?.length > 0"
                        class="overflow-x-auto"
                    >
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-[11px] text-slate-400 uppercase tracking-wider bg-slate-50/50"
                                >
                                    <th class="px-6 py-3 font-semibold">
                                        Customer Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold"
                                    >
                                        Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr
                                    v-for="customer in stats.top_customers"
                                    :key="customer.id"
                                    class="hover:bg-slate-50 transition-colors duration-200"
                                >
                                    <td
                                        class="px-6 py-4 text-sm font-medium text-slate-700"
                                    >
                                        {{ customer.name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right text-sm text-red-600 font-mono font-medium"
                                    >
                                        {{
                                            formatCurrency(customer.total_debt)
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else
                        class="py-12 text-center text-sm text-slate-400 font-light"
                    >
                        No active debt records found.
                    </div>
                </div>

                <div
                    class="bg-white border border-slate-200 rounded-md shadow-sm overflow-hidden"
                >
                    <div
                        class="px-6 py-4 border-b border-slate-50 bg-slate-900"
                    >
                        <h3
                            class="text-xs font-bold text-slate-200 uppercase tracking-widest flex items-center gap-2"
                        >
                            <span
                                class="w-2 h-2 rounded-full bg-red-500 animate-pulse"
                            ></span>
                            Blacklisted Members
                        </h3>
                    </div>

                    <div
                        v-if="stats.blacklist_customers?.length > 0"
                        class="overflow-x-auto"
                    >
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="text-[11px] text-slate-400 uppercase tracking-wider bg-slate-50"
                                >
                                    <th class="px-6 py-3 font-semibold">
                                        Full Name
                                    </th>
                                    <th
                                        class="px-6 py-3 text-right font-semibold"
                                    >
                                        Contact
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr
                                    v-for="customer in stats.blacklist_customers"
                                    :key="customer.id"
                                    class="hover:bg-red-50/30 transition-colors duration-200"
                                >
                                    <td
                                        class="px-6 py-4 text-sm text-slate-700"
                                    >
                                        {{ customer.name }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right text-sm text-slate-500 font-light italic"
                                    >
                                        {{ customer.whatsapp_number }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-else
                        class="py-12 text-center text-sm text-slate-400 font-light"
                    >
                        Clear record. No users on blacklist.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../Layout/AppLayout.vue";
import StatCard from "../Components/StatCard.vue";

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_customers: 0,
            total_paid: 0,
            total_pending: 0,
            total_overdue: 0,
            top_customers: [],
            blacklist_customers: [],
        }),
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value || 0);
};
</script>
