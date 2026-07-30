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
                class="btn-secondary"
                @click="router.patch(route('maintenance.complete', task.id))"
            >
                Marcar completada ✓
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
                        class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold capitalize text-slate-600"
                    >
                        {{ task.category }}
                    </span>
                </div>
                <h3 class="mt-8 text-xs font-bold uppercase tracking-widest text-slate-400">
                    Descripción y checklist
                </h3>
                <p class="mt-3 whitespace-pre-line leading-7 text-slate-700">
                    {{ task.description || 'No se ha añadido una descripción detallada.' }}
                </p>
            </article>
            <aside class="space-y-5">
                <section class="panel p-5">
                    <h3 class="font-bold">Planificación</h3>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="text-xs font-bold uppercase text-slate-400">Web</dt>
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
                            <dt class="text-xs font-bold uppercase text-slate-400">Técnico</dt>
                            <dd class="mt-1">{{ task.assignee?.name || 'Sin asignar' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase text-slate-400">Programada</dt>
                            <dd class="mt-1">{{ date(task.scheduled_at) }}</dd>
                        </div>
                        <div v-if="task.completed_at">
                            <dt class="text-xs font-bold uppercase text-slate-400">Completada</dt>
                            <dd class="mt-1 text-emerald-700">{{ date(task.completed_at) }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
