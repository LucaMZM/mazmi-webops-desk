<script setup>
import { reactive } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import PriorityBadge from '@/Components/UI/PriorityBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
const props = defineProps({ tasks: Object, filters: Object, websites: Array });
const user = usePage().props.auth.user;
const form = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    category: props.filters.category || '',
    website_id: props.filters.website_id || '',
    schedule: props.filters.schedule || '',
});
const apply = () =>
    router.get(route('maintenance.index'), form, { preserveState: true, replace: true });
const canEdit = (t) =>
    user.role === 'admin' || (user.role === 'technician' && t.assigned_to === user.id);
const complete = (t) =>
    router.patch(route('maintenance.complete', t.id), {}, { preserveScroll: true });
const date = (v) =>
    v
        ? new Intl.DateTimeFormat('es-ES', {
              day: '2-digit',
              month: 'short',
              year: 'numeric',
          }).format(new Date(v))
        : 'Sin programar';
const overdue = (t) =>
    t.scheduled_at && new Date(t.scheduled_at) < new Date() && t.status !== 'completed';
const statusLabel = {
    pending: 'Pendiente',
    in_progress: 'En curso',
    completed: 'Completada',
    blocked: 'Bloqueada',
};
const priorityLabel = { low: 'Baja', medium: 'Media', high: 'Alta' };
const categoryLabel = {
    backups: 'Copias de seguridad',
    updates: 'Actualizaciones',
    security: 'Seguridad',
    performance: 'Rendimiento',
    content: 'Contenido',
    seo: 'SEO',
    other: 'Otros',
};
</script>
<template>
    <Head title="Mantenimiento" />
    <AuthenticatedLayout title="Mantenimiento">
        <PageHeader
            eyebrow="Operaciones"
            title="Tareas de mantenimiento"
            description="Trabajo preventivo y correctivo programado por web."
        >
            <Link
                v-if="user.role === 'admin'"
                :href="route('maintenance.create')"
                class="btn-primary"
            >
                <AppIcon name="plus" :size="17" />
                Nueva tarea
            </Link>
        </PageHeader>
        <div class="mb-4 flex flex-wrap gap-2">
            <button
                v-for="item in [
                    { v: '', l: 'Todas' },
                    { v: 'overdue', l: 'Atrasadas' },
                    { v: 'upcoming', l: 'Próximas 30 días' },
                ]"
                :key="item.v"
                :class="form.schedule === item.v ? 'btn-primary' : 'btn-secondary'"
                type="button"
                @click="
                    form.schedule = item.v;
                    apply();
                "
            >
                {{ item.l }}
            </button>
        </div>
        <form
            class="filter-bar md:grid-cols-2 xl:grid-cols-[1fr_repeat(4,160px)_auto]"
            @submit.prevent="apply"
        >
            <input v-model="form.search" class="rounded-lg text-sm" placeholder="Buscar tarea…" />
            <select v-model="form.status" class="rounded-lg text-sm">
                <option value="">Estado</option>
                <option
                    v-for="x in ['pending', 'in_progress', 'completed', 'blocked']"
                    :key="x"
                    :value="x"
                >
                    {{ statusLabel[x] }}
                </option>
            </select>
            <select v-model="form.priority" class="rounded-lg text-sm">
                <option value="">Prioridad</option>
                <option v-for="x in ['low', 'medium', 'high']" :key="x" :value="x">
                    {{ priorityLabel[x] }}
                </option>
            </select>
            <select v-model="form.category" class="rounded-lg text-sm">
                <option value="">Categoría</option>
                <option
                    v-for="x in [
                        'backups',
                        'updates',
                        'security',
                        'performance',
                        'content',
                        'seo',
                        'other',
                    ]"
                    :key="x"
                    :value="x"
                >
                    {{ categoryLabel[x] }}
                </option>
            </select>
            <select v-model="form.website_id" class="rounded-lg text-sm">
                <option value="">Web</option>
                <option v-for="w in websites" :key="w.id" :value="w.id">{{ w.name }}</option>
            </select>
            <button class="btn-secondary">Filtrar</button>
        </form>
        <div v-if="tasks.data.length" class="table-shell">
            <div
                class="table-head hidden grid-cols-[140px_minmax(260px,1.4fr)_minmax(190px,1fr)_auto_110px] items-center gap-5 px-5 py-3 xl:grid"
            >
                <span>Planificación</span>
                <span>Tarea / web</span>
                <span>Responsable</span>
                <span>Estado</span>
                <span class="text-right">Acción</span>
            </div>
            <article
                v-for="task in tasks.data"
                :key="task.id"
                class="grid gap-4 border-b border-slate-100 p-5 transition last:border-b-0 hover:bg-slate-50/70 xl:grid-cols-[140px_minmax(260px,1.4fr)_minmax(190px,1fr)_auto_110px] xl:items-center"
            >
                <div>
                    <p
                        :class="
                            overdue(task)
                                ? 'font-bold text-red-700'
                                : 'font-semibold text-slate-700'
                        "
                        class="text-xs"
                    >
                        {{ overdue(task) ? 'Atrasada' : date(task.scheduled_at) }}
                    </p>
                    <p v-if="overdue(task)" class="mt-1 text-[11px] text-red-500">
                        {{ date(task.scheduled_at) }}
                    </p>
                </div>
                <div class="min-w-0">
                    <Link
                        :href="route('maintenance.show', task.id)"
                        class="font-bold text-slate-950 hover:text-indigo-600"
                    >
                        {{ task.title }}
                    </Link>
                    <p class="mt-1 truncate text-xs text-slate-500">
                        {{ task.website.name }} · {{ task.website.client.company_name }}
                    </p>
                    <p
                        v-if="task.description"
                        class="mt-2 line-clamp-1 text-xs text-slate-500 xl:hidden"
                    >
                        {{ task.description }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-700">
                        {{ task.assignee?.name || 'Sin asignar' }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">{{ categoryLabel[task.category] }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <PriorityBadge :priority="task.priority" />
                    <StatusBadge :status="task.status" />
                </div>
                <div class="xl:text-right">
                    <button
                        v-if="canEdit(task) && task.status !== 'completed'"
                        type="button"
                        class="inline-flex min-h-9 items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800"
                        @click="complete(task)"
                    >
                        Completar
                        <AppIcon name="check" :size="14" />
                    </button>
                    <Link
                        v-else
                        :href="route('maintenance.show', task.id)"
                        class="text-xs font-bold text-indigo-600"
                    >
                        Ver detalle →
                    </Link>
                </div>
            </article>
        </div>
        <div v-else class="panel">
            <EmptyState
                title="No hay tareas con estos filtros"
                description="Prueba otra vista o programa una nueva tarea."
            >
                <Link
                    v-if="user.role === 'admin'"
                    :href="route('maintenance.create')"
                    class="btn-primary"
                >
                    Crear tarea
                </Link>
            </EmptyState>
        </div>
        <div class="mt-5 border-t border-slate-200"><Pagination :links="tasks.links" /></div>
    </AuthenticatedLayout>
</template>
