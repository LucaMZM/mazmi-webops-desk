<script setup>
import AppIcon from '@/Components/UI/AppIcon.vue';

defineProps({
    label: String,
    value: [String, Number],
    hint: String,
    tone: { type: String, default: 'indigo' },
    icon: String,
});
const tones = {
    indigo: 'bg-indigo-50 text-indigo-700',
    emerald: 'bg-emerald-50 text-emerald-700',
    amber: 'bg-amber-50 text-amber-700',
    red: 'bg-red-50 text-red-700',
    blue: 'bg-blue-50 text-blue-700',
};
</script>
<template>
    <div
        class="panel p-4 transition duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md sm:p-5"
    >
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold leading-5 text-slate-500 sm:text-[13px]">
                    {{ label }}
                </p>
                <p
                    class="mt-1.5 text-2xl font-extrabold tracking-tight text-slate-950 sm:mt-2 sm:text-3xl"
                >
                    {{ value }}
                </p>
                <p v-if="hint" class="mt-1 text-xs font-medium text-slate-400">{{ hint }}</p>
            </div>
            <div :class="['grid h-9 w-9 shrink-0 place-items-center rounded-lg', tones[tone]]">
                <AppIcon :name="icon" :size="18" />
            </div>
        </div>
    </div>
</template>
