<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import ConfirmDeleteModal from '@/Components/UI/ConfirmDeleteModal.vue';
const props = defineProps({ ticket: Object });
const user = usePage().props.auth.user;
const canEdit = computed(
    () =>
        user.role === 'admin' ||
        (user.role === 'technician' && props.ticket.assigned_to === user.id),
);
const updating = ref(false);
const setStatus = (status) => {
    if (updating.value) return;
    router.patch(
        route('tickets.status', props.ticket.id),
        { status },
        { onStart: () => (updating.value = true), onFinish: () => (updating.value = false) },
    );
};
const date = (v) =>
    v ? new Intl.DateTimeFormat('es-ES', { dateStyle: 'long' }).format(new Date(v)) : 'No indicada';
</script>
<template>
    <Head :title="`Ticket #${ticket.id}`" />
    <AuthenticatedLayout title="Detalle de ticket">
        <PageHeader
            eyebrow="Soporte"
            :title="`#${ticket.id} · ${ticket.title}`"
            :description="ticket.client.company_name"
            :back-href="route('tickets.index')"
        >
            <Link v-if="canEdit" :href="route('tickets.edit', ticket.id)" class="btn-primary">
                Editar
            </Link>
            <ConfirmDeleteModal
                v-if="user.role === 'admin'"
                :url="route('tickets.destroy', ticket.id)"
            />
        </PageHeader>
        <div class="detail-layout">
            <article class="dossier p-5 sm:p-7">
                <div class="flex flex-wrap gap-2">
                    <PriorityBadge :priority="ticket.priority" />
                    <StatusBadge :status="ticket.status" />
                </div>
                <h2 class="meta-label mt-7">Descripción</h2>
                <p class="mt-3 whitespace-pre-line leading-7 text-slate-700">
                    {{ ticket.description }}
                </p>
                <div v-if="canEdit" class="mt-8 border-t border-slate-100 pt-6">
                    <p class="mb-3 text-sm font-bold">Actualizar estado</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="s in [
                                'open',
                                'in_progress',
                                'waiting_client',
                                'resolved',
                                'closed',
                            ]"
                            :key="s"
                            type="button"
                            :disabled="ticket.status === s || updating"
                            :aria-busy="updating"
                            class="btn-secondary disabled:bg-teal-50 disabled:text-teal-700"
                            @click="setStatus(s)"
                        >
                            {{
                                {
                                    open: 'Abierto',
                                    in_progress: 'En curso',
                                    waiting_client: 'Espera cliente',
                                    resolved: 'Resuelto',
                                    closed: 'Cerrado',
                                }[s]
                            }}
                        </button>
                    </div>
                </div>
            </article>
            <aside class="space-y-5">
                <section class="dossier p-5 sm:p-6">
                    <h2 class="section-heading">Datos del ticket</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="meta-label">Web</dt>
                            <dd class="mt-1">
                                <Link
                                    v-if="ticket.website"
                                    :href="route('websites.show', ticket.website.id)"
                                    class="font-semibold text-teal-600"
                                >
                                    {{ ticket.website.name }}
                                </Link>
                                <span v-else>Solicitud general</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="meta-label">Técnico</dt>
                            <dd class="mt-1">{{ ticket.assignee?.name || 'Sin asignar' }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Fecha objetivo</dt>
                            <dd class="mt-1">{{ date(ticket.due_date) }}</dd>
                        </div>
                        <div>
                            <dt class="meta-label">Creado</dt>
                            <dd class="mt-1">{{ date(ticket.created_at) }}</dd>
                        </div>
                        <div v-if="ticket.resolved_at">
                            <dt class="meta-label">Resuelto</dt>
                            <dd class="mt-1">{{ date(ticket.resolved_at) }}</dd>
                        </div>
                    </dl>
                </section>
                <section class="border-l-2 border-slate-300 px-5 py-2">
                    <p class="text-xs font-medium text-slate-700">Buena práctica</p>
                    <p class="mt-2 text-xs leading-6 text-slate-500">
                        No compartas contraseñas en el ticket. Usa un gestor seguro y un canal
                        autorizado.
                    </p>
                </section>
            </aside>
        </div>
    </AuthenticatedLayout>
</template>
