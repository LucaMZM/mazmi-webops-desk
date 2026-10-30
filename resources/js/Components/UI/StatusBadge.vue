<script setup>
defineProps({ status: String });
const labels = {
    active: 'Activo',
    inactive: 'Inactivo',
    stable: 'Estable',
    review: 'Revisión',
    incident: 'Incidencia',
    critical: 'Crítico',
    open: 'Abierto',
    in_progress: 'En curso',
    waiting_client: 'Espera cliente',
    resolved: 'Resuelto',
    closed: 'Cerrado',
    pending: 'Pendiente',
    completed: 'Completada',
    blocked: 'Bloqueada',
    good: 'Correcto',
    attention: 'Atención',
    expiring: 'Próximo a vencer',
    expired: 'Caducado',
    unknown: 'Desconocido',
};
const colors = {
    active: 'bg-emerald-50 text-emerald-700',
    stable: 'bg-emerald-50 text-emerald-700',
    good: 'bg-emerald-50 text-emerald-700',
    completed: 'bg-emerald-50 text-emerald-700',
    review: 'bg-amber-50 text-amber-700',
    attention: 'bg-amber-50 text-amber-700',
    expiring: 'bg-amber-50 text-amber-700',
    incident: 'bg-orange-50 text-orange-700',
    critical: 'bg-red-50 text-red-700',
    expired: 'bg-red-50 text-red-700',
    blocked: 'bg-red-50 text-red-700',
    open: 'bg-blue-50 text-blue-700',
    in_progress: 'bg-teal-50 text-teal-700',
    waiting_client: 'bg-amber-50 text-amber-800',
    resolved: 'bg-emerald-50 text-emerald-700',
    closed: 'bg-slate-100 text-slate-600',
    inactive: 'bg-slate-100 text-slate-600',
    pending: 'bg-slate-100 text-slate-700',
    unknown: 'bg-slate-100 text-slate-600',
};
</script>
<template>
    <span
        :class="[
            'inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-white/50 px-2.5 py-1 text-xs font-medium',
            colors[status] || colors.unknown,
        ]"
    >
        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" />
        {{ labels[status] || status }}
    </span>
</template>
