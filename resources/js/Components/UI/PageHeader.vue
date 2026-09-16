<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ eyebrow: String, title: String, description: String, backHref: String });
</script>
<template>
    <div
        class="mb-6 flex flex-col justify-between gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-end"
    >
        <div>
            <Link
                v-if="backHref"
                :href="backHref"
                class="mb-3 inline-flex min-h-9 items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800"
            >
                ← Volver
            </Link>
            <p
                v-if="eyebrow"
                class="text-[11px] font-bold uppercase tracking-[0.14em] text-indigo-600"
            >
                {{ eyebrow }}
            </p>
            <h1
                class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-[1.75rem]"
            >
                {{ title }}
            </h1>
            <p v-if="description" class="mt-1.5 max-w-2xl text-sm leading-6 text-slate-500">
                {{ description }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2"><slot /></div>
    </div>
</template>
