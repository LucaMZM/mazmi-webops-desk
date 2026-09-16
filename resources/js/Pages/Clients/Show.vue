<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import ConfirmDeleteModal from '@/Components/UI/ConfirmDeleteModal.vue';
defineProps({ client: Object });
const user = usePage().props.auth.user;
const months = [
    '',
    'Enero',
    'Febrero',
    'Marzo',
    'Abril',
    'Mayo',
    'Junio',
    'Julio',
    'Agosto',
    'Septiembre',
    'Octubre',
    'Noviembre',
    'Diciembre',
];
</script>
<template>
    <Head :title="client.company_name" />
    <AuthenticatedLayout title="Detalle de cliente">
        <PageHeader
            eyebrow="Cliente"
            :title="client.company_name"
            :description="`${client.contact_name} · ${client.email}`"
            :back-href="route('clients.index')"
        >
            <Link
                v-if="user.role === 'admin'"
                :href="route('clients.edit', client.id)"
                class="btn-secondary"
            >
                Editar
            </Link>
            <ConfirmDeleteModal
                v-if="user.role === 'admin'"
                :url="route('clients.destroy', client.id)"
                message="Solo se eliminará si no tiene webs, tickets, reportes o usuarios asociados."
            />
        </PageHeader>
        <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
            <div class="space-y-6">
                <section class="panel">
                    <div class="flex items-center justify-between border-b border-slate-100 p-5">
                        <h2 class="section-heading">
                            Webs gestionadas
                            <span class="text-slate-400">({{ client.websites_count }})</span>
                        </h2>
                        <Link
                            v-if="user.role === 'admin'"
                            :href="route('websites.create', { client_id: client.id })"
                            class="text-sm font-semibold text-indigo-600"
                        >
                            Añadir web →
                        </Link>
                    </div>
                    <div v-if="client.websites.length" class="divide-y divide-slate-100">
                        <Link
                            v-for="web in client.websites"
                            :key="web.id"
                            :href="route('websites.show', web.id)"
                            class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-slate-50"
                        >
                            <div class="min-w-0">
                                <p class="font-bold">{{ web.name }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ web.url }}</p>
                                <p class="mt-1 text-xs font-semibold text-indigo-600">
                                    {{ web.technology }} · {{ web.maintenance_plan }}
                                </p>
                            </div>
                            <StatusBadge :status="web.status" />
                        </Link>
                    </div>
                    <EmptyState v-else title="Sin webs asociadas" />
                </section>
                <section class="panel">
                    <div class="flex items-center justify-between border-b border-slate-100 p-5">
                        <h2 class="section-heading">Tickets recientes</h2>
                        <Link
                            :href="route('tickets.index', { client_id: client.id })"
                            class="text-sm font-semibold text-indigo-600"
                        >
                            Ver todos →
                        </Link>
                    </div>
                    <div v-if="client.tickets.length" class="divide-y divide-slate-100">
                        <Link
                            v-for="ticket in client.tickets"
                            :key="ticket.id"
                            :href="route('tickets.show', ticket.id)"
                            class="flex flex-col gap-2 p-4 hover:bg-slate-50 sm:flex-row sm:items-center"
                        >
                            <div class="flex-1">
                                <p class="text-sm font-semibold">
                                    #{{ ticket.id }} · {{ ticket.title }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ ticket.assignee?.name || 'Sin asignar' }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <PriorityBadge :priority="ticket.priority" />
                                <StatusBadge :status="ticket.status" />
                            </div>
                        </Link>
                    </div>
                    <EmptyState v-else title="Sin tickets" />
                </section>
            </div>
            <aside class="space-y-6">
                <section class="panel p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="section-heading">Ficha</h2>
                        <StatusBadge :status="client.status" />
                    </div>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="meta-label">Contacto</dt>
                            <dd class="mt-1 font-medium">{{ client.contact_name }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Email</dt>
                            <dd class="mt-1 break-all">{{ client.email }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Teléfono</dt>
                            <dd class="mt-1">{{ client.phone || 'No indicado' }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Ciudad</dt>
                            <dd class="mt-1">{{ client.city || 'No indicada' }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Notas</dt>
                            <dd class="mt-1 whitespace-pre-line text-slate-600">
                                {{ client.notes || 'Sin notas' }}
                            </dd>
                        </div>
                    </dl>
                </section>
                <section class="panel p-5">
                    <h2 class="section-heading">Reportes mensuales</h2>
                    <div
                        v-if="client.reports.length"
                        class="mt-4 divide-y divide-slate-100 border-y border-slate-100"
                    >
                        <Link
                            v-for="report in client.reports"
                            :key="report.id"
                            :href="route('reports.show', report.id)"
                            class="flex items-center justify-between py-3 text-sm font-semibold transition hover:text-indigo-600"
                        >
                            <span>{{ months[report.month] }} {{ report.year }}</span>
                            <StatusBadge :status="report.general_status" />
                        </Link>
                    </div>
                    <p v-else class="mt-3 text-sm text-slate-500">Todavía no hay reportes.</p>
                </section>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
