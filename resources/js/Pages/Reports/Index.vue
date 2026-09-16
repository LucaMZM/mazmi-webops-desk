<script setup>
import { reactive } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
const props = defineProps({ reports: Object, filters: Object, clients: Array });
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
const form = reactive({
    client_id: props.filters.client_id || '',
    month: props.filters.month || '',
    year: props.filters.year || '',
    general_status: props.filters.general_status || '',
});
const apply = () =>
    router.get(route('reports.index'), form, { preserveState: true, replace: true });
</script>
<template>
    <Head title="Reportes" />
    <AuthenticatedLayout title="Reportes">
        <PageHeader
            eyebrow="Seguimiento"
            title="Reportes mensuales"
            description="Resumen ejecutivo de actividad, incidencias y recomendaciones para cada cliente."
        >
            <Link v-if="user.role === 'admin'" :href="route('reports.create')" class="btn-primary">
                <AppIcon name="plus" :size="17" />
                Crear reporte
            </Link>
        </PageHeader>
        <form
            class="filter-bar md:grid-cols-2 xl:grid-cols-[repeat(4,1fr)_auto]"
            @submit.prevent="apply"
        >
            <select v-if="clients.length" v-model="form.client_id" class="rounded-lg text-sm">
                <option value="">Todos los clientes</option>
                <option v-for="c in clients" :key="c.id" :value="c.id">
                    {{ c.company_name }}
                </option>
            </select>
            <select v-model="form.month" class="rounded-lg text-sm">
                <option value="">Todos los meses</option>
                <option v-for="i in 12" :key="i" :value="i">{{ months[i] }}</option>
            </select>
            <input v-model="form.year" type="number" class="rounded-lg text-sm" placeholder="Año" />
            <select v-model="form.general_status" class="rounded-lg text-sm">
                <option value="">Todos los estados</option>
                <option value="good">Correcto</option>
                <option value="attention">Atención</option>
                <option value="critical">Crítico</option>
            </select>
            <button class="btn-secondary">Filtrar</button>
        </form>
        <div v-if="reports.data.length" class="table-shell">
            <div
                class="table-head hidden grid-cols-[150px_minmax(220px,1.3fr)_130px_260px_32px] items-center gap-5 px-5 py-3 lg:grid"
            >
                <span>Periodo</span>
                <span>Cliente / resumen</span>
                <span>Estado</span>
                <span>Actividad</span>
                <span></span>
            </div>
            <Link
                v-for="report in reports.data"
                :key="report.id"
                :href="route('reports.show', report.id)"
                class="group grid gap-4 border-b border-slate-100 p-5 transition last:border-b-0 hover:bg-slate-50/70 lg:grid-cols-[150px_minmax(220px,1.3fr)_130px_260px_32px] lg:items-center"
            >
                <div>
                    <p class="text-sm font-bold text-slate-900">
                        {{ months[report.month] }}
                    </p>
                    <p class="text-xs text-slate-500">{{ report.year }}</p>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-950 group-hover:text-indigo-600">
                        {{ report.client.company_name }}
                    </h3>
                    <p class="mt-1 line-clamp-1 text-xs leading-5 text-slate-500">
                        {{ report.summary }}
                    </p>
                </div>
                <div><StatusBadge :status="report.general_status" /></div>
                <div class="grid grid-cols-3 divide-x divide-slate-200 text-center">
                    <div class="px-2">
                        <p class="text-base font-black">{{ report.completed_tasks_count }}</p>
                        <p class="text-[9px] uppercase text-slate-400">Tareas</p>
                    </div>
                    <div class="px-2">
                        <p class="text-base font-black text-emerald-600">
                            {{ report.resolved_tickets_count }}
                        </p>
                        <p class="text-[9px] uppercase text-slate-400">Resueltos</p>
                    </div>
                    <div class="px-2">
                        <p
                            :class="report.pending_tickets_count ? 'text-amber-600' : ''"
                            class="text-base font-black"
                        >
                            {{ report.pending_tickets_count }}
                        </p>
                        <p class="text-[9px] uppercase text-slate-400">Pendientes</p>
                    </div>
                </div>
                <span class="text-right text-indigo-600">→</span>
            </Link>
        </div>
        <div v-else class="panel">
            <EmptyState
                title="No hay reportes con estos filtros"
                description="Los reportes mensuales aparecerán aquí."
            >
                <Link
                    v-if="user.role === 'admin'"
                    :href="route('reports.create')"
                    class="btn-primary"
                >
                    Crear reporte
                </Link>
            </EmptyState>
        </div>
        <div class="mt-5 border-t border-slate-200"><Pagination :links="reports.links" /></div>
    </AuthenticatedLayout>
</template>
