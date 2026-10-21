<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/UI/AppIcon.vue';
defineProps({ eyebrow: String, title: String, description: String, backHref: String });
</script>
<template>
    <div class="mb-7 flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
        <div class="min-w-0">
            <Link
                v-if="backHref"
                :href="backHref"
                class="mb-3 inline-flex min-h-9 items-center gap-2 rounded-md text-sm text-slate-500 hover:text-indigo-700"
            >
                <AppIcon name="arrow-left" :size="15" />
                {{ eyebrow || 'Volver' }}
            </Link>
            <h1
                class="font-display text-[1.75rem] font-bold leading-tight tracking-[-0.045em] text-slate-900 sm:text-[2rem]"
            >
                {{ title }}
            </h1>
            <p v-if="description" class="mt-2 max-w-[65ch] text-sm leading-6 text-slate-500">
                {{ description }}
            </p>
        </div>
        <div v-if="$slots.default" class="flex shrink-0 flex-wrap items-center gap-2"><slot /></div>
    </div>
</template>
