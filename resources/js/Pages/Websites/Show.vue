<script setup>
import AppIcon from '@/Components/UI/AppIcon.vue';
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
            eyebrow="Webs"
            :title="website.name"
            :description="website.client.company_name"
            :back-href="route('websites.index')"
        >
            <a :href="website.url" target="_blank" rel="noopener noreferrer" class="btn-secondary">
                Visitar web
                <AppIcon name="external" :size="15" />
            </a>
            <Link
                v-if="user.role === 'admin'"
                :href="route('websites.edit', website.id)"
                class="btn-primary"
            >
                Editar ficha
            </Link>
            <ConfirmDeleteModal
                v-if="user.role === 'admin'"
                :url="route('websites.destroy', website.id)"
                message="Solo se eliminará si no existen tickets ni tareas asociadas."
            />
        </PageHeader>
        <section class="dossier mb-6 overflow-hidden">
            <header class="section-topline">
                <div class="flex min-w-0 items-center gap-3">
                    <span
                        class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-indigo-50 text-indigo-700"
                    >
                        <AppIcon name="websites" :size="20" />
                    </span>
                    <div class="min-w-0">
                        <h2 class="section-heading">Ficha de servicio</h2>
                        <a
                            :href="website.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-0.5 block truncate text-xs text-slate-500 hover:text-indigo-700"
                        >
                            {{ website.url }}
                        </a>
                    </div>
                </div>
                <StatusBadge :status="website.status" />
            </header>
            <dl class="grid gap-x-8 gap-y-6 p-5 sm:grid-cols-2 sm:p-6 xl:grid-cols-4">
                <div>
                    <dt class="meta-label">Cliente</dt>
                    <dd class="mt-2 text-sm font-medium">
                        <Link
                            :href="route('clients.show', website.client.id)"
                            class="hover:text-indigo-700"
                        >
                            {{ website.client.company_name }}
                        </Link>
                    </dd>
                </div>
                <div>
                    <dt class="meta-label">Tecnología</dt>
                    <dd class="mt-2 text-sm font-medium">
                        {{ technologyLabel(website.technology) }}
                    </dd>
                </div>
                <div>
                    <dt class="meta-label">Plan de mantenimiento</dt>
                    <dd class="mt-2 text-sm font-medium">
                        {{ planLabel(website.maintenance_plan) }}
                    </dd>
                </div>
                <div>
                    <dt class="meta-label">Proveedor de hosting</dt>
                    <dd class="mt-2 text-sm font-medium">
                        {{ website.hosting_provider || 'No indicado' }}
                    </dd>
                </div>
            </dl>
        </section>
        <div class="detail-layout">
            <div class="dossier overflow-hidden">
                <section>
                    <header class="section-topline">
                        <div>
                            <h2 class="section-heading">Tickets asociados</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Seguimiento de incidencias y solicitudes
                            </p>
                        </div>
                        <Link
                            v-if="user.role !== 'technician'"
                            :href="route('tickets.create', { website_id: website.id })"
                            class="btn-text"
                        >
                            <AppIcon name="plus" :size="15" />
                            Nuevo ticket
                        </Link>
                    </header>
                    <div v-if="website.tickets.length" class="divide-y divide-slate-100">
                        <Link
                            v-for="ticket in website.tickets"
                            :key="ticket.id"
                            :href="route('tickets.show', ticket.id)"
                            class="flex flex-col gap-3 p-5 hover:bg-slate-50 sm:flex-row sm:items-center sm:px-6"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">
                                    <span class="mr-2 text-xs text-slate-500">
                                        #{{ ticket.id }}
                                    </span>
                                    {{ ticket.title }}
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ ticket.assignee?.name || 'Sin asignar' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <PriorityBadge :priority="ticket.priority" />
                                <StatusBadge :status="ticket.status" />
                            </div>
                        </Link>
                    </div>
                    <EmptyState
                        v-else
                        title="Sin tickets asociados"
                        description="Las solicitudes relacionadas con esta web aparecerán aquí."
                    />
                </section>
                <section class="border-t border-slate-200">
                    <header class="section-topline">
                        <h2 class="section-heading">Mantenimiento</h2>
                        <Link
                            v-if="user.role === 'admin'"
                            :href="route('maintenance.create', { website_id: website.id })"
                            class="btn-text"
                        >
                            <AppIcon name="plus" :size="15" />
                            Nueva tarea
                        </Link>
                    </header>
                    <div v-if="website.maintenance_tasks.length" class="divide-y divide-slate-100">
                        <Link
                            v-for="task in website.maintenance_tasks"
                            :key="task.id"
                            :href="route('maintenance.show', task.id)"
                            class="flex flex-col justify-between gap-3 p-5 hover:bg-slate-50 sm:flex-row sm:items-center sm:px-6"
                        >
                            <div class="min-w-0">
                                <p class="text-sm font-medium">{{ task.title }}</p>
                                <p class="mt-1 text-xs leading-5 text-slate-500">
                                    {{ categoryLabel(task.category) }}
                                    <span class="mx-2">/</span>
                                    {{ fullDate(task.scheduled_at) }}
                                </p>
                            </div>
                            <StatusBadge :status="task.status" />
                        </Link>
                    </div>
                    <EmptyState v-else title="Sin tareas programadas" />
                </section>
            </div>
            <aside class="dossier">
                <section class="dossier-section">
                    <h2 class="section-heading">Vencimientos y SSL</h2>
                    <dl class="mt-5 space-y-5">
                        <div>
                            <dt class="meta-label">Dominio</dt>
                            <dd class="mt-1.5 text-sm font-medium">
                                {{ fullDate(website.domain_expires_at) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="meta-label">Hosting</dt>
                            <dd class="mt-1.5 text-sm font-medium">
                                {{ fullDate(website.hosting_expires_at) }}
                            </dd>
                        </div>
                        <div
                            class="flex items-center justify-between border-t border-slate-100 pt-4"
                        >
                            <dt class="text-xs text-slate-500">Certificado SSL</dt>
                            <dd><StatusBadge :status="website.ssl_status" /></dd>
                        </div>
                    </dl>
                </section>
                <section class="dossier-section">
                    <h2 class="section-heading">Notas técnicas</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">
                        {{ website.notes || 'Sin notas técnicas.' }}
                    </p>
                </section>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
