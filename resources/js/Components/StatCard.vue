<template>
    <div
        class="bg-white p-5 rounded-xl border border-slate-100 shadow-sm shadow-slate-200/50 flex items-center space-x-4"
    >
        <div :class="['p-2 rounded-md ', colorClass]">
            <slot name="icon"></slot>
        </div>
        <div>
            <p
                class="text-xs text-slate-400 font-semibold uppercase tracking-wider"
            >
                {{ label }}
            </p>
            <p class="text-xl font-bold text-slate-800">{{ value }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps(["label", "value", "colorClass"]);
</script>
