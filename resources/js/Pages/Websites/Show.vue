<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import ConfirmDeleteModal from '@/Components/UI/ConfirmDeleteModal.vue';
defineProps({ website: Object });
const user = usePage().props.auth.user;
const fullDate = (v) =>
    v
        ? new Intl.DateTimeFormat('es-ES', { dateStyle: 'medium' }).format(new Date(v))
        : 'No indicado';
const technologyLabel = (value) =>
    ({ 'PHP custom': 'PHP a medida', 'Static HTML': 'HTML estático', Other: 'Otro' })[value] ||
    value;
const planLabel = (value) =>
    ({ basic: 'Básico', standard: 'Estándar', premium: 'Premium', none: 'Sin plan' })[value] ||
    value;
const categoryLabel = (value) =>
    ({
        backups: 'Copias de seguridad',
        updates: 'Actualizaciones',
        security: 'Seguridad',
        performance: 'Rendimiento',
        content: 'Contenido',
        seo: 'SEO',
        other: 'Otro',
    })[value] || value;
</script>
<template>
    <Head :title="website.name" />
    <AuthenticatedLayout title="Detalle web">
        <PageHeader
            eyebrow="Web gestionada"
            :title="website.name"
            :description="website.client.company_name"
            :back-href="route('websites.index')"
        >
            <a :href="website.url" target="_blank" rel="noopener" class="btn-secondary">
                Abrir web ↗
            </a>
            <Link
                v-if="user.role === 'admin'"
                :href="route('websites.edit', website.id)"
                class="btn-primary"
            >
                Editar
            </Link>
            <ConfirmDeleteModal
                v-if="user.role === 'admin'"
                :url="route('websites.destroy', website.id)"
                message="Solo se eliminará si no existen tickets ni tareas asociadas."
            />
        </PageHeader>
        <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
            <div class="space-y-6">
                <section class="panel">
                    <div class="flex justify-between border-b border-slate-100 p-5">
                        <h2 class="section-heading">Tickets asociados</h2>
                        <Link
                            v-if="user.role !== 'technician'"
                            :href="route('tickets.create', { website_id: website.id })"
                            class="text-sm font-semibold text-indigo-600"
                        >
                            Nuevo ticket →
                        </Link>
                    </div>
                    <div v-if="website.tickets.length" class="divide-y divide-slate-100">
                        <Link
                            v-for="ticket in website.tickets"
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
                    <EmptyState v-else title="Sin tickets asociados" />
                </section>
                <section class="panel">
                    <div class="flex justify-between border-b border-slate-100 p-5">
                        <h2 class="section-heading">Mantenimiento</h2>
                        <Link
                            v-if="user.role === 'admin'"
                            :href="route('maintenance.create', { website_id: website.id })"
                            class="text-sm font-semibold text-indigo-600"
                        >
                            Nueva tarea →
                        </Link>
                    </div>
                    <div v-if="website.maintenance_tasks.length" class="divide-y divide-slate-100">
                        <Link
                            v-for="task in website.maintenance_tasks"
                            :key="task.id"
                            :href="route('maintenance.show', task.id)"
                            class="flex items-center gap-3 p-4 hover:bg-slate-50"
                        >
                            <div class="flex-1">
                                <p class="text-sm font-semibold">{{ task.title }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ categoryLabel(task.category) }} ·
                                    {{ fullDate(task.scheduled_at) }}
                                </p>
                            </div>
                            <StatusBadge :status="task.status" />
                        </Link>
                    </div>
                    <EmptyState v-else title="Sin tareas programadas" />
                </section>
            </div>
            <aside class="space-y-6">
                <section class="panel p-5">
                    <div class="flex justify-between">
                        <h2 class="section-heading">Estado técnico</h2>
                        <StatusBadge :status="website.status" />
                    </div>
                    <dl class="mt-5 grid gap-4 text-sm">
                        <div>
                            <dt class="meta-label">URL</dt>
                            <dd class="mt-1 break-all font-medium text-indigo-600">
                                {{ website.url }}
                            </dd>
                        </div>
                        <div>
                            <dt class="meta-label">Tecnología</dt>
                            <dd class="mt-1">{{ technologyLabel(website.technology) }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Plan</dt>
                            <dd class="mt-1">{{ planLabel(website.maintenance_plan) }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Hosting</dt>
                            <dd class="mt-1">{{ website.hosting_provider || 'No indicado' }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">SSL</dt>
                            <dd class="mt-1"><StatusBadge :status="website.ssl_status" /></dd>
                        </div>
                    </dl>
                </section>
                <section class="panel p-5">
                    <h2 class="section-heading">Vencimientos</h2>
                    <div
                        class="mt-4 grid grid-cols-2 divide-x divide-slate-200 border-y border-slate-200"
                    >
                        <div class="py-3 pr-3">
                            <p class="text-xs text-slate-400">Dominio</p>
                            <p class="mt-1 text-sm font-bold">
                                {{ fullDate(website.domain_expires_at) }}
                            </p>
                        </div>
                        <div class="py-3 pl-3">
                            <p class="text-xs text-slate-400">Hosting</p>
                            <p class="mt-1 text-sm font-bold">
                                {{ fullDate(website.hosting_expires_at) }}
                            </p>
                        </div>
                    </div>
                </section>
                <section class="panel p-5">
                    <h2 class="section-heading">Notas</h2>
                    <p class="mt-3 whitespace-pre-line text-sm text-slate-600">
                        {{ website.notes || 'Sin notas técnicas.' }}
                    </p>
                </section>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
