<script setup>
defineProps({
    title: { type: String, default: 'Todavía no hay datos' },
    description: { type: String, default: 'Los nuevos registros aparecerán aquí.' },
});
</script>
<template>
    <div class="py-14 text-center">
        <div
            class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-2xl text-slate-400"
        >
            ◇
        </div>
        <h3 class="mt-4 font-bold text-slate-900">{{ title }}</h3>
        <p class="mx-auto mt-1 max-w-sm text-sm text-slate-500">{{ description }}</p>
        <div class="mt-5"><slot /></div>
    </div>
</template>
