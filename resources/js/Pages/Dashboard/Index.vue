<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';

const props = defineProps({ metrics:Object, latestTickets:Array, upcomingTasks:Array, ticketsByStatus:Object, websitesByTechnology:Array });
const user = computed(() => usePage().props.auth.user);
const statusLabels={open:'Abiertos',in_progress:'En curso',waiting_client:'Esperando cliente',resolved:'Resueltos',closed:'Cerrados'};
const statusColors={open:'bg-blue-500',in_progress:'bg-indigo-500',waiting_client:'bg-violet-500',resolved:'bg-emerald-500',closed:'bg-slate-400'};
const maxTech = computed(() => Math.max(...props.websitesByTechnology.map(x=>x.total),1));
const date = value => value ? new Intl.DateTimeFormat('es-ES',{day:'2-digit',month:'short'}).format(new Date(value)) : 'Sin fecha';
</script>

<template><Head title="Dashboard" /><AuthenticatedLayout title="Dashboard">
    <div class="mb-7 flex flex-col justify-between gap-3 sm:flex-row sm:items-end"><div><p class="text-sm font-medium text-slate-500">Vista operativa · {{ new Intl.DateTimeFormat('es-ES',{weekday:'long',day:'numeric',month:'long'}).format(new Date()) }}</p><h2 class="mt-1 text-2xl font-black text-slate-950">Hola, {{ user.name.split(' ')[0] }}</h2><p class="mt-1 text-sm text-slate-500">Estas son las prioridades que requieren atención.</p></div><Link v-if="user.role !== 'client'" :href="route('tickets.create')" class="btn-primary">+ Nuevo ticket</Link><Link v-else :href="route('tickets.create')" class="btn-primary">Solicitar soporte</Link></div>
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <StatCard label="Clientes activos" :value="metrics.active_clients" icon="C" tone="indigo" />
        <StatCard label="Webs gestionadas" :value="metrics.websites" icon="W" tone="blue" />
        <StatCard label="Tickets abiertos" :value="metrics.open_tickets" :hint="`${metrics.urgent_tickets} urgentes`" icon="!" :tone="metrics.urgent_tickets ? 'red' : 'emerald'" />
        <StatCard label="Tareas pendientes" :value="metrics.pending_tasks" :hint="`${metrics.overdue_tasks} atrasadas`" icon="✓" :tone="metrics.overdue_tasks ? 'amber' : 'emerald'" />
        <StatCard label="Dominios próximos" :value="metrics.expiring_domains" hint="Vencen en 30 días" icon="D" :tone="metrics.expiring_domains ? 'amber' : 'emerald'" />
        <StatCard label="Hostings próximos" :value="metrics.expiring_hosting" hint="Vencen en 30 días" icon="H" :tone="metrics.expiring_hosting ? 'amber' : 'emerald'" />
        <StatCard label="Tickets urgentes" :value="metrics.urgent_tickets" icon="↑" :tone="metrics.urgent_tickets ? 'red' : 'emerald'" />
        <StatCard label="Tareas atrasadas" :value="metrics.overdue_tasks" icon="⌁" :tone="metrics.overdue_tasks ? 'red' : 'emerald'" />
    </section>
    <section class="mt-6 grid gap-6 xl:grid-cols-5">
        <div class="panel xl:col-span-3"><div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><div><h3 class="font-bold text-slate-950">Últimos tickets</h3><p class="text-xs text-slate-500">Actividad reciente del soporte</p></div><Link :href="route('tickets.index')" class="text-sm font-semibold text-indigo-600">Ver todos →</Link></div><div v-if="latestTickets.length" class="divide-y divide-slate-100"><Link v-for="ticket in latestTickets" :key="ticket.id" :href="route('tickets.show',ticket.id)" class="flex flex-col gap-3 px-5 py-4 transition hover:bg-slate-50 sm:flex-row sm:items-center"><div class="min-w-0 flex-1"><div class="flex items-center gap-2"><span class="text-xs font-bold text-slate-400">#{{ ticket.id }}</span><h4 class="truncate text-sm font-semibold text-slate-900">{{ ticket.title }}</h4></div><p class="mt-1 truncate text-xs text-slate-500">{{ ticket.client.company_name }} · {{ ticket.website?.name || 'General' }}</p></div><div class="flex items-center gap-2"><PriorityBadge :priority="ticket.priority"/><StatusBadge :status="ticket.status"/></div></Link></div><EmptyState v-else title="Sin tickets recientes" /></div>
        <div class="panel p-5 xl:col-span-2"><h3 class="font-bold text-slate-950">Tickets por estado</h3><p class="mb-5 text-xs text-slate-500">Distribución de la carga actual</p><div class="space-y-4"><div v-for="(label,key) in statusLabels" :key="key"><div class="mb-1.5 flex justify-between text-xs font-semibold"><span>{{ label }}</span><span>{{ ticketsByStatus[key] || 0 }}</span></div><div class="h-2.5 overflow-hidden rounded-full bg-slate-100"><div :class="['h-full rounded-full',statusColors[key]]" :style="{width:`${Math.min(100,((ticketsByStatus[key]||0)/Math.max(Object.values(ticketsByStatus).reduce((a,b)=>a+b,0),1))*100)}%`}" /></div></div></div></div>
    </section>
    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="panel"><div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><h3 class="font-bold text-slate-950">Próximo mantenimiento</h3><Link :href="route('maintenance.index',{schedule:'upcoming'})" class="text-sm font-semibold text-indigo-600">Agenda →</Link></div><div v-if="upcomingTasks.length" class="divide-y divide-slate-100"><Link v-for="task in upcomingTasks" :key="task.id" :href="route('maintenance.show',task.id)" class="flex items-center gap-4 px-5 py-4 hover:bg-slate-50"><div class="w-14 rounded-xl bg-slate-100 px-2 py-2 text-center text-xs font-bold text-slate-600">{{ date(task.scheduled_at) }}</div><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold">{{ task.title }}</p><p class="truncate text-xs text-slate-500">{{ task.website.name }} · {{ task.website.client.company_name }}</p></div><StatusBadge :status="task.status"/></Link></div><EmptyState v-else title="Agenda despejada" description="No hay tareas programadas próximamente." /></div>
        <div class="panel p-5"><h3 class="font-bold text-slate-950">Tecnologías gestionadas</h3><p class="mb-5 text-xs text-slate-500">Composición del inventario técnico</p><div v-if="websitesByTechnology.length" class="space-y-4"><div v-for="item in websitesByTechnology" :key="item.technology" class="grid grid-cols-[100px_1fr_30px] items-center gap-3"><span class="truncate text-xs font-semibold text-slate-600">{{ item.technology }}</span><div class="h-3 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-indigo-500" :style="{width:`${item.total/maxTech*100}%`}" /></div><span class="text-right text-xs font-bold">{{ item.total }}</span></div></div><EmptyState v-else /></div>
    </section>
</AuthenticatedLayout></template>
