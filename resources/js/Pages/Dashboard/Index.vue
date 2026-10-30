<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import WorkspaceOnboarding from '@/Components/UI/WorkspaceOnboarding.vue';

const props = defineProps({
    metrics: Object,
    latestTickets: Array,
    upcomingTasks: Array,
    ticketsByStatus: Object,
    websitesByTechnology: Array,
});
const user = computed(() => usePage().props.auth.user);
const isNewWorkspace = computed(() => user.value.role === 'admin' && Number(props.metrics.clients) === 0 && Number(props.metrics.websites) === 0);
const statusLabels = {
    open: 'Abiertos',
    in_progress: 'En curso',
    waiting_client: 'Esperando cliente',
    resolved: 'Resueltos',
    closed: 'Cerrados',
};
const statusColors = {
    open: 'bg-teal-600',
    in_progress: 'bg-sky-400',
    waiting_client: 'bg-amber-400',
    resolved: 'bg-emerald-500',
    closed: 'bg-slate-300',
};
const ticketTotal = computed(() =>
    Object.values(props.ticketsByStatus).reduce((sum, value) => sum + Number(value), 0),
);
const technologyLabel = {
    'PHP custom': 'PHP a medida',
    'Static HTML': 'HTML estático',
    Other: 'Otro',
};
const date = (value) =>
    value
        ? new Intl.DateTimeFormat('es-ES', { day: '2-digit', month: 'short' }).format(
              new Date(value),
          )
        : 'Sin fecha';
</script>

<template>
    <Head title="Resumen de operaciones" />
    <AuthenticatedLayout title="Dashboard">
        <PageHeader
            title="Resumen de operaciones"
            :description="`Hola, ${user.name.split(' ')[0]}. Este es el trabajo que tienes por delante.`"
        >
            <Link
                v-if="user.role !== 'technician'"
                :href="route('tickets.create')"
                class="btn-primary"
            >
                <AppIcon name="plus" :size="17" />
                {{ user.role === 'client' ? 'Solicitar soporte' : 'Nuevo ticket' }}
            </Link>
        </PageHeader>

        <WorkspaceOnboarding v-if="isNewWorkspace" :clients="metrics.clients" :websites="metrics.websites" />

        <section
            v-if="!isNewWorkspace"
            class="glass-hero mb-7 grid overflow-hidden xl:grid-cols-[1.15fr_1fr]"
            data-glass-optic
            aria-label="Carga de trabajo"
        >
            <div class="relative border-b border-slate-200 p-6 sm:p-7 xl:border-b-0 xl:border-r">
                <div class="flex items-center gap-2.5 text-sm text-slate-600">
                    <AppIcon name="tickets" :size="18" />
                    <span>Soporte en curso</span>
                </div>
                <div class="mt-4 flex items-baseline gap-3">
                    <span class="stat-number text-6xl">{{ metrics.open_tickets }}</span>
                    <span class="text-base text-slate-600">tickets activos</span>
                </div>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <Link
                        :href="route('tickets.index', { priority: 'urgent' })"
                        :class="[
                            'inline-flex min-h-9 items-center gap-2 rounded-md px-2.5 text-xs font-medium',
                            metrics.urgent_tickets
                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                : 'bg-slate-50 text-slate-600 hover:bg-slate-100',
                        ]"
                    >
                        <span class="h-1.5 w-1.5 rounded-full bg-current" />
                        {{ metrics.urgent_tickets }} urgentes
                    </Link>
                    <Link :href="route('tickets.index')" class="btn-text">
                        Abrir soporte
                        <AppIcon name="arrow-right" :size="16" />
                    </Link>
                </div>
            </div>
            <div class="grid grid-cols-2 divide-x divide-slate-200">
                <Link :href="route('maintenance.index')" class="group p-6 hover:bg-slate-50 sm:p-7">
                    <div class="flex items-center justify-between text-slate-500">
                        <AppIcon name="maintenance" :size="19" />
                        <AppIcon
                            name="chevron-right"
                            :size="16"
                            class="text-slate-400 group-hover:text-teal-600"
                        />
                    </div>
                    <p class="stat-number mt-4 text-4xl">{{ metrics.pending_tasks }}</p>
                    <h2 class="mt-1 font-sans text-sm font-medium">Tareas pendientes</h2>
                    <p
                        :class="[
                            'mt-3 text-xs',
                            metrics.overdue_tasks ? 'text-amber-800' : 'text-slate-500',
                        ]"
                    >
                        {{ metrics.overdue_tasks }} fuera de plazo
                    </p>
                </Link>
                <Link
                    :href="route('websites.index', { expiring: '1' })"
                    class="group p-6 hover:bg-slate-50 sm:p-7"
                >
                    <div class="flex items-center justify-between text-slate-500">
                        <AppIcon name="clock" :size="19" />
                        <AppIcon
                            name="chevron-right"
                            :size="16"
                            class="text-slate-400 group-hover:text-teal-600"
                        />
                    </div>
                    <p class="stat-number mt-4 text-4xl">
                        {{ Number(metrics.expiring_domains) + Number(metrics.expiring_hosting) }}
                    </p>
                    <h2 class="mt-1 font-sans text-sm font-medium">Próximos vencimientos</h2>
                    <p class="mt-3 text-xs leading-5 text-slate-500">
                        {{ metrics.expiring_domains }} dominios y
                        {{ metrics.expiring_hosting }} servicios de hosting en 30 días
                    </p>
                </Link>
            </div>
        </section>

        <div
            class="grid items-start gap-6 2xl:grid-cols-[minmax(0,1.6fr)_minmax(320px,1fr)] xl:grid-cols-[minmax(0,1.4fr)_minmax(300px,1fr)]"
        >
            <section class="panel" data-glass-optic>
                <header class="section-topline">
                    <div>
                        <h2 class="section-heading">Actividad de soporte</h2>
                        <p class="mt-1 text-xs text-slate-500">Últimos tickets registrados</p>
                    </div>
                    <Link :href="route('tickets.index')" class="btn-text">
                        Ver tickets
                        <AppIcon name="arrow-right" :size="15" />
                    </Link>
                </header>
                <div v-if="latestTickets.length" class="divide-y divide-slate-100">
                    <Link
                        v-for="ticket in latestTickets"
                        :key="ticket.id"
                        :href="route('tickets.show', ticket.id)"
                        class="group flex flex-col gap-3 px-5 py-4 hover:bg-slate-50 sm:px-6"
                    >
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 w-7 shrink-0 text-xs tabular-nums text-slate-500">
                                #{{ ticket.id }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3
                                    class="font-sans text-sm font-medium text-slate-900 group-hover:text-teal-700"
                                >
                                    {{ ticket.title }}
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ ticket.client.company_name }}
                                </p>
                            </div>
                            <AppIcon
                                name="chevron-right"
                                :size="15"
                                class="mt-1 shrink-0 text-slate-400"
                            />
                        </div>
                        <div class="ml-10 flex flex-wrap items-center gap-2">
                            <StatusBadge :status="ticket.status" />
                            <PriorityBadge :priority="ticket.priority" />
                            <span v-if="ticket.assignee" class="ml-auto text-xs text-slate-500">
                                {{ ticket.assignee.name }}
                            </span>
                        </div>
                    </Link>
                </div>
                <EmptyState
                    v-else
                    title="Sin tickets recientes"
                    description="Las solicitudes de soporte aparecerán aquí."
                />
            </section>
            <div class="space-y-6">
                <section class="panel">
                    <header class="section-topline">
                        <div>
                            <h2 class="section-heading">Próximas tareas</h2>
                            <p class="mt-1 text-xs text-slate-500">Mantenimiento programado</p>
                        </div>
                        <Link
                            :href="route('maintenance.index', { schedule: 'upcoming' })"
                            class="btn-text"
                            aria-label="Ver próximas tareas"
                        >
                            <AppIcon name="arrow-right" :size="17" />
                        </Link>
                    </header>
                    <div v-if="upcomingTasks.length" class="divide-y divide-slate-100">
                        <Link
                            v-for="task in upcomingTasks"
                            :key="task.id"
                            :href="route('maintenance.show', task.id)"
                            class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50 sm:px-6"
                        >
                            <time class="mt-0.5 w-12 shrink-0 text-xs font-medium text-teal-700">
                                {{ date(task.scheduled_at) }}
                            </time>
                            <div class="min-w-0">
                                <h3 class="font-sans text-sm font-medium">{{ task.title }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ task.website.name }}</p>
                            </div>
                        </Link>
                    </div>
                    <EmptyState
                        v-else
                        title="Sin tareas próximas"
                        description="No hay mantenimientos programados pendientes."
                    />
                </section>
                <section class="panel p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="section-heading">Balance de tickets</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ ticketTotal }} tickets en total
                            </p>
                        </div>
                        <AppIcon name="tickets" :size="17" class="text-slate-400" />
                    </div>
                    <div
                        class="my-5 flex h-2.5 gap-1 overflow-hidden rounded-full"
                        aria-hidden="true"
                    >
                        <div
                            v-for="(label, key) in statusLabels"
                            :key="key"
                            :class="statusColors[key]"
                            :style="{
                                width:
                                    ((ticketsByStatus[key] || 0) / Math.max(ticketTotal, 1)) * 100 +
                                    '%',
                            }"
                        />
                    </div>
                    <dl class="space-y-3">
                        <div
                            v-for="(label, key) in statusLabels"
                            :key="key"
                            class="flex items-center justify-between text-xs"
                        >
                            <dt class="flex items-center gap-2 text-slate-600">
                                <span :class="['h-2 w-2 rounded-full', statusColors[key]]" />
                                {{ label }}
                            </dt>
                            <dd class="font-medium text-slate-900">
                                {{ ticketsByStatus[key] || 0 }}
                            </dd>
                        </div>
                    </dl>
                </section>
            </div>
        </div>
        <section class="mt-7 border-t border-slate-200 pt-6">
            <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="section-heading">Inventario bajo gestión</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ metrics.websites }} webs de {{ metrics.active_clients }} clientes activos
                    </p>
                </div>
                <Link :href="route('websites.index')" class="btn-text">
                    Explorar webs
                    <AppIcon name="arrow-right" :size="16" />
                </Link>
            </div>
            <dl
                v-if="websitesByTechnology.length"
                class="grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3 xl:grid-cols-6"
            >
                <div
                    v-for="item in websitesByTechnology"
                    :key="item.technology"
                    class="border-l-2 border-slate-200 pl-4"
                >
                    <dt class="text-xs text-slate-500">
                        {{ technologyLabel[item.technology] || item.technology }}
                    </dt>
                    <dd class="stat-number mt-1 text-2xl">{{ item.total }}</dd>
                </div>
            </dl>
            <EmptyState v-else title="Sin webs registradas" description="Cuando añadas tu primer cliente y su sitio web, aquí aparecerá el inventario." />
        </section>
    </AuthenticatedLayout>
</template>
