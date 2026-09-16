<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import ConfirmDeleteModal from '@/Components/UI/ConfirmDeleteModal.vue';
const props = defineProps({ task: Object });
const user = usePage().props.auth.user;
const canEdit = computed(
    () =>
        user.role === 'admin' || (user.role === 'technician' && props.task.assigned_to === user.id),
);
const date = (v) =>
    v
        ? new Intl.DateTimeFormat('es-ES', { dateStyle: 'long', timeStyle: 'short' }).format(
              new Date(v),
          )
        : 'No indicada';
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
    <Head :title="task.title" />
    <AuthenticatedLayout title="Detalle de tarea">
        <PageHeader
            eyebrow="Mantenimiento"
            :title="task.title"
            :description="`${task.website.name} · ${task.website.client.company_name}`"
            :back-href="route('maintenance.index')"
        >
            <button
                v-if="canEdit && task.status !== 'completed'"
                type="button"
                class="btn-secondary"
                @click="router.patch(route('maintenance.complete', task.id))"
            >
                Marcar como completada
            </button>
            <Link v-if="canEdit" :href="route('maintenance.edit', task.id)" class="btn-primary">
                Editar
            </Link>
            <ConfirmDeleteModal
                v-if="user.role === 'admin'"
                :url="route('maintenance.destroy', task.id)"
            />
        </PageHeader>
        <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
            <article class="panel p-5 sm:p-7">
                <div class="flex flex-wrap gap-2">
                    <PriorityBadge :priority="task.priority" />
                    <StatusBadge :status="task.status" />
                    <span
                        class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600"
                    >
                        {{ categoryLabel(task.category) }}
                    </span>
                </div>
                <h2 class="meta-label mt-8">Descripción y checklist</h2>
                <p class="mt-3 whitespace-pre-line leading-7 text-slate-700">
                    {{ task.description || 'No se ha añadido una descripción detallada.' }}
                </p>
            </article>
            <aside class="space-y-5">
                <section class="panel p-5">
                    <h2 class="section-heading">Planificación</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="meta-label">Web</dt>
                            <dd class="mt-1">
                                <Link
                                    :href="route('websites.show', task.website.id)"
                                    class="font-semibold text-indigo-600"
                                >
                                    {{ task.website.name }}
                                </Link>
                            </dd>
                        </div>
                        <div>
                            <dt class="meta-label">Técnico</dt>
                            <dd class="mt-1">{{ task.assignee?.name || 'Sin asignar' }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Programada</dt>
                            <dd class="mt-1">{{ date(task.scheduled_at) }}</dd>
                        </div>
                        <div v-if="task.completed_at">
                            <dt class="meta-label">Completada</dt>
                            <dd class="mt-1 text-emerald-700">{{ date(task.completed_at) }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
