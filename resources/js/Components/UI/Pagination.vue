<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/UI/AppIcon.vue';
defineProps({ links: Array });
const isPrevious = (label) => /previous|anterior|«/i.test(label);
const isNext = (label) => /next|siguiente|»/i.test(label);
const labelText = (label) =>
    isPrevious(label) ? 'Anterior' : isNext(label) ? 'Siguiente' : label.replace(/<[^>]*>/g, '');
</script>
<template>
    <nav
        v-if="links?.length > 3"
        aria-label="Paginación"
        class="flex flex-wrap items-center justify-center gap-1 border-t border-slate-200 px-4 py-4 sm:justify-end"
    >
        <component
            :is="link.url ? Link : 'span'"
            v-for="link in links"
            :key="`${link.label}-${link.url ?? 'disabled'}`"
            :href="link.url"
            preserve-scroll
            :aria-current="link.active ? 'page' : undefined"
            :aria-disabled="!link.url || undefined"
            :class="[
                'inline-flex min-h-10 min-w-10 items-center justify-center gap-2 rounded-lg px-3 text-sm',
                link.active
                    ? 'bg-teal-50 font-semibold text-teal-700'
                    : link.url
                      ? 'text-slate-600 hover:bg-slate-50'
                      : 'text-slate-400',
            ]"
        >
            <AppIcon v-if="isPrevious(link.label)" name="arrow-left" :size="14" />
            {{ labelText(link.label) }}
            <AppIcon v-if="isNext(link.label)" name="arrow-right" :size="14" />
        </component>
    </nav>
</template>
