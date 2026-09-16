<script setup>
import AppIcon from '@/Components/UI/AppIcon.vue';

defineProps({
    title: { type: String, default: 'Todavía no hay datos' },
    description: { type: String, default: 'Los nuevos registros aparecerán aquí.' },
});
</script>
<template>
    <div class="px-5 py-12 text-center">
        <div
            class="mx-auto grid h-11 w-11 place-items-center rounded-md border border-slate-200 bg-slate-50 text-slate-400"
        >
            <AppIcon name="inbox" class="h-5 w-5" />
        </div>
        <h3 class="mt-4 text-sm font-bold text-slate-900">{{ title }}</h3>
        <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">{{ description }}</p>
        <div class="mt-5"><slot /></div>
    </div>
</template>
