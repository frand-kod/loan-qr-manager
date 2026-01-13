<template>
    <AppLayout>
        <div class="space-y-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-500">
                    Welcome back! Here's an overview of your loan management.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <StatCard
                    label="Total Customers"
                    :value="stats.total_customers.toString()"
                    colorClass="bg-blue-100 text-blue-600"
                />
                <StatCard
                    label="Total Paid"
                    :value="formatCurrency(stats.total_paid || 0)"
                    colorClass="bg-green-50 text-green-600"
                />
                <StatCard
                    label="Pending Amount"
                    :value="formatCurrency(stats.total_pending || 0)"
                    colorClass="bg-yellow-50 text-yellow-600"
                />
                <StatCard
                    label="Overdue Amount"
                    :value="formatCurrency(stats.total_overdue || 0)"
                    colorClass="bg-red-50 text-red-600"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl shadow-sm p-6 min-h-[200px]">
                    <h3 class="font-normal text-gray-800 mb-4 border-b pb-2">
                        Recent Pending Debts
                    </h3>
                    <div
                        class="flex items-center justify-center h-32 text-gray-400"
                    >
                        No pending debts
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 min-h-[200px]">
                    <h3 class="font-normal text-gray-800 mb-4 border-b pb-2">
                        Recent Paid Debts
                    </h3>
                    <div
                        class="flex items-center justify-center h-32 text-gray-400"
                    >
                        No paid debts
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-normal text-gray-800 mb-4 border-b pb-2">
                    Top Customers by Total Debt
                </h3>
                <div
                    class="flex items-center justify-center h-32 text-gray-400"
                >
                    No customers with debts
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "../Layout/AppLayout.vue";
import StatCard from "../Components/StatCard.vue";

// Data ini nantinya akan dikirim dari Controller
const props = defineProps({
    stats: Object,
});

const formatCurrency = (value) => {
    if (!value) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
</script>
