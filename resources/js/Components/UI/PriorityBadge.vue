<script setup>
defineProps({ priority: String });
const labels = { low: 'Baja', medium: 'Media', high: 'Alta', urgent: 'Urgente' };
const colors = {
    low: 'bg-slate-100 text-slate-600',
    medium: 'bg-blue-50 text-blue-700',
    high: 'bg-orange-50 text-orange-700',
    urgent: 'bg-red-50 text-red-700',
};
</script>
<template>
    <span :class="['inline-flex rounded-full px-2.5 py-1 text-xs font-bold', colors[priority]]">
        {{ labels[priority] }}
    </span>
</template>
