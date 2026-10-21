<script setup>
import AppIcon from '@/Components/UI/AppIcon.vue';

defineProps({
    title: { type: String, default: 'Todavía no hay datos' },
    description: { type: String, default: 'Los nuevos registros aparecerán aquí.' },
});
</script>
<template>
    <div class="px-5 py-14 text-center">
        <div
            class="mx-auto grid h-11 w-11 place-items-center rounded-full bg-slate-50 text-slate-500"
        >
            <AppIcon name="inbox" class="h-5 w-5" />
        </div>
        <h3 class="mt-4 text-base font-semibold text-slate-900">{{ title }}</h3>
        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">{{ description }}</p>
        <div class="mt-5"><slot /></div>
    </div>
</template>
