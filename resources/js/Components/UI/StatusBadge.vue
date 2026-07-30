<script setup>
const props = defineProps({ status: String });
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
    active: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    stable: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    good: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    completed: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    review: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    attention: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    expiring: 'bg-amber-50 text-amber-700 ring-amber-600/20',
    incident: 'bg-orange-50 text-orange-700 ring-orange-600/20',
    critical: 'bg-red-50 text-red-700 ring-red-600/20',
    expired: 'bg-red-50 text-red-700 ring-red-600/20',
    blocked: 'bg-red-50 text-red-700 ring-red-600/20',
    open: 'bg-blue-50 text-blue-700 ring-blue-600/20',
    in_progress: 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
    waiting_client: 'bg-violet-50 text-violet-700 ring-violet-600/20',
    resolved: 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    closed: 'bg-slate-100 text-slate-600 ring-slate-500/20',
    inactive: 'bg-slate-100 text-slate-600 ring-slate-500/20',
    pending: 'bg-slate-100 text-slate-700 ring-slate-500/20',
    unknown: 'bg-slate-100 text-slate-600 ring-slate-500/20',
};
</script>
<template>
    <span
        :class="[
            'inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset',
            colors[status] || colors.unknown,
        ]"
    >
        {{ labels[status] || status }}
    </span>
</template>
