<script setup>
import AppIcon from '@/Components/UI/AppIcon.vue';

defineProps({
    title: { type: String, default: 'Tu espacio está listo' },
    description: { type: String, default: 'Todavía no hay registros. Añade el primero para empezar a trabajar.' },
    icon: { type: String, default: 'inbox' },
});
</script>
<template>
    <div class="webops-empty-state">
        <div class="webops-empty-icon"><AppIcon :name="icon" :size="24" /></div>
        <h3 class="mt-5">{{ title }}</h3>
        <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-slate-600">{{ description }}</p>
        <div v-if="$slots.default" class="mt-6 flex flex-wrap justify-center gap-2"><slot /></div>
    </div>
</template>
