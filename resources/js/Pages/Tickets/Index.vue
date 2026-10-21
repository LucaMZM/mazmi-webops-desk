<script setup>
import { reactive, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
const props = defineProps({ tickets: Object, filters: Object, clients: Array, technicians: Array });
const user = usePage().props.auth.user;
const form = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    client_id: props.filters.client_id || '',
    assigned_to: props.filters.assigned_to || '',
});
const apply = () =>
    router.get(route('tickets.index'), form, { preserveState: true, replace: true });
const canEdit = (t) =>
    user.role === 'admin' || (user.role === 'technician' && t.assigned_to === user.id);
const updatingTicket = ref(null);
const setStatus = (t, status) => {
    if (updatingTicket.value !== null) return;
    router.patch(
        route('tickets.status', t.id),
        { status },
        {
            preserveScroll: true,
            onStart: () => (updatingTicket.value = t.id),
            onFinish: () => (updatingTicket.value = null),
        },
    );
};
const statusLabel = {
    open: 'Abierto',
    in_progress: 'En curso',
    waiting_client: 'Espera cliente',
    resolved: 'Resuelto',
    closed: 'Cerrado',
};
</script>
<template>
    <Head title="Tickets" />
    <AuthenticatedLayout title="Tickets">
        <PageHeader
            eyebrow="Soporte"
            title="Tickets"
            description="Incidencias y solicitudes priorizadas con trazabilidad de estado."
        >
            <Link
                v-if="user.role !== 'technician'"
                :href="route('tickets.create')"
                class="btn-primary"
            >
                <AppIcon name="plus" :size="17" />
                Nuevo ticket
            </Link>
        </PageHeader>
        <div class="collection-heading">
            <p>
                <strong>{{ tickets.total }}</strong>
                tickets{{
                    Object.values(filters).some(Boolean)
                        ? ' con los filtros actuales'
                        : ' en el registro'
                }}
            </p>
            <span class="text-xs">
                Página {{ tickets.current_page }} de {{ tickets.last_page }}
            </span>
        </div>
        <form
            class="filter-bar md:grid-cols-2 xl:grid-cols-[1fr_repeat(4,160px)_auto]"
            @submit.prevent="apply"
        >
            <input
                v-model="form.search"
                class="rounded-lg text-sm"
                placeholder="Buscar ticket…"
                aria-label="Buscar tickets"
                name="search"
            />
            <select
                v-model="form.status"
                class="rounded-lg text-sm"
                aria-label="Filtrar por estado"
                name="status"
            >
                <option value="">Estado</option>
                <option
                    v-for="x in ['open', 'in_progress', 'waiting_client', 'resolved', 'closed']"
                    :key="x"
                    :value="x"
                >
                    {{
                        {
                            open: 'Abierto',
                            in_progress: 'En curso',
                            waiting_client: 'Espera cliente',
                            resolved: 'Resuelto',
                            closed: 'Cerrado',
                        }[x]
                    }}
                </option>
            </select>
            <select
                v-model="form.priority"
                class="rounded-lg text-sm"
                aria-label="Filtrar por prioridad"
                name="priority"
            >
                <option value="">Prioridad</option>
                <option v-for="x in ['low', 'medium', 'high', 'urgent']" :key="x" :value="x">
                    {{ { low: 'Baja', medium: 'Media', high: 'Alta', urgent: 'Urgente' }[x] }}
                </option>
            </select>
            <select
                v-if="clients.length"
                v-model="form.client_id"
                class="rounded-lg text-sm"
                aria-label="Filtrar por cliente"
                name="client_id"
            >
                <option value="">Cliente</option>
                <option v-for="c in clients" :key="c.id" :value="c.id">
                    {{ c.company_name }}
                </option>
            </select>
            <select
                v-if="technicians.length"
                v-model="form.assigned_to"
                class="rounded-lg text-sm"
                aria-label="Filtrar por técnico"
                name="assigned_to"
            >
                <option value="">Técnico</option>
                <option v-for="t in technicians" :key="t.id" :value="t.id">
                    {{ t.name }}
                </option>
            </select>
            <button class="btn-secondary">Filtrar</button>
        </form>
        <div class="table-shell">
            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full">
                    <thead class="table-head">
                        <tr>
                            <th scope="col" class="table-cell">Ticket</th>
                            <th scope="col" class="table-cell">Cliente / web</th>
                            <th scope="col" class="table-cell">Prioridad</th>
                            <th scope="col" class="table-cell">Asignado</th>
                            <th scope="col" class="table-cell">Estado</th>
                            <th scope="col" class="table-cell">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="ticket in tickets.data"
                            :key="ticket.id"
                            class="transition hover:bg-slate-50/80"
                        >
                            <td class="table-cell">
                                <Link
                                    :href="route('tickets.show', ticket.id)"
                                    class="font-bold text-slate-900"
                                >
                                    #{{ ticket.id }} · {{ ticket.title }}
                                </Link>
                                <p class="mt-1 text-xs text-slate-500">
                                    Vence:
                                    {{
                                        ticket.due_date
                                            ? new Date(ticket.due_date).toLocaleDateString('es-ES')
                                            : 'Sin fecha'
                                    }}
                                </p>
                            </td>
                            <td class="table-cell">
                                <p class="text-sm font-semibold">
                                    {{ ticket.client.company_name }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ ticket.website?.name || 'Solicitud general' }}
                                </p>
                            </td>
                            <td class="table-cell">
                                <PriorityBadge :priority="ticket.priority" />
                            </td>
                            <td class="table-cell text-sm">
                                {{ ticket.assignee?.name || 'Sin asignar' }}
                            </td>
                            <td class="table-cell"><StatusBadge :status="ticket.status" /></td>
                            <td class="table-cell">
                                <select
                                    v-if="canEdit(ticket)"
                                    :value="ticket.status"
                                    :disabled="updatingTicket !== null"
                                    :aria-busy="updatingTicket === ticket.id"
                                    :aria-label="`Estado del ticket #${ticket.id}`"
                                    class="min-h-9 rounded-lg border-slate-300 py-1.5 text-xs"
                                    @change="setStatus(ticket, $event.target.value)"
                                >
                                    <option
                                        v-for="x in [
                                            'open',
                                            'in_progress',
                                            'waiting_client',
                                            'resolved',
                                            'closed',
                                        ]"
                                        :key="x"
                                        :value="x"
                                    >
                                        {{ statusLabel[x] }}
                                    </option>
                                </select>
                                <Link
                                    v-else
                                    :href="route('tickets.show', ticket.id)"
                                    class="text-sm font-semibold text-indigo-600"
                                >
                                    Ver →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="divide-y divide-slate-100 xl:hidden">
                <Link
                    v-for="ticket in tickets.data"
                    :key="ticket.id"
                    :href="route('tickets.show', ticket.id)"
                    class="block p-4"
                >
                    <div class="flex justify-between gap-3">
                        <p class="font-bold">#{{ ticket.id }} · {{ ticket.title }}</p>
                        <PriorityBadge :priority="ticket.priority" />
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        {{ ticket.client.company_name }} · {{ ticket.website?.name || 'General' }}
                    </p>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-xs">{{ ticket.assignee?.name || 'Sin asignar' }}</span>
                        <StatusBadge :status="ticket.status" />
                    </div>
                </Link>
            </div>
            <EmptyState v-if="!tickets.data.length" title="No hay tickets con estos filtros" />
            <Pagination :links="tickets.links" />
        </div>
    </AuthenticatedLayout>
</template>
