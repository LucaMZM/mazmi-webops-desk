<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/UI/AppIcon.vue';
defineProps({ eyebrow: String, title: String, description: String, backHref: String });
</script>
<template>
    <div class="mb-8 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
        <div class="min-w-0">
            <Link
                v-if="backHref"
                :href="backHref"
                class="mb-3 inline-flex min-h-9 items-center gap-2 rounded-md text-sm text-slate-500 hover:text-teal-700"
            >
                <AppIcon name="arrow-left" :size="15" />
                {{ eyebrow || 'Volver' }}
            </Link>
            <h1
                class="font-display text-[2.1rem] font-semibold leading-[1.12] tracking-[-0.058em] text-slate-900 sm:text-[2.6rem]"
            >
                {{ title }}
            </h1>
            <p v-if="description" class="mt-3 max-w-[65ch] text-sm leading-7 text-slate-600">
                {{ description }}
            </p>
        </div>
        <div v-if="$slots.default" class="flex shrink-0 flex-wrap items-center gap-2"><slot /></div>
    </div>
</template>
