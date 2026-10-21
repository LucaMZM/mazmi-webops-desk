<script setup>
defineProps({ priority: String });
const labels = { low: 'Baja', medium: 'Media', high: 'Alta', urgent: 'Urgente' };
const levels = { low: 1, medium: 2, high: 3, urgent: 4 };
const colors = {
    low: 'text-slate-500',
    medium: 'text-slate-600',
    high: 'text-orange-700',
    urgent: 'text-red-700',
};
</script>
<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 whitespace-nowrap text-xs font-medium',
            colors[priority],
        ]"
    >
        <svg viewBox="0 0 16 16" width="15" height="15" fill="currentColor" aria-hidden="true">
            <rect
                v-for="level in 4"
                :key="level"
                :x="1 + (level - 1) * 4"
                :y="13 - level * 2.5"
                width="2.5"
                :height="level * 2.5"
                rx=".7"
                :opacity="level <= levels[priority] ? 1 : 0.18"
            />
        </svg>
        {{ labels[priority] }}
    </span>
</template>
