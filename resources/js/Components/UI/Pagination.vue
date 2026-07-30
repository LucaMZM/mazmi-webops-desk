<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ links: Array });
</script>
<template>
    <nav
        v-if="links?.length > 3"
        class="flex flex-wrap justify-center gap-1 border-t border-slate-200 px-4 py-4"
    >
        <component
            :is="link.url ? Link : 'span'"
            v-for="link in links"
            :key="`${link.label}-${link.url ?? 'disabled'}`"
            :href="link.url"
            preserve-scroll
            :class="[
                'rounded-lg px-3 py-2 text-sm font-semibold',
                link.active
                    ? 'bg-indigo-600 text-white'
                    : link.url
                      ? 'text-slate-600 hover:bg-slate-100'
                      : 'cursor-not-allowed text-slate-300',
            ]"
        >
            <span v-html="link.label" />
        </component>
    </nav>
</template>
