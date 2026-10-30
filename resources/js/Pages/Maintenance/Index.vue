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
const completingTask = ref(null);
const complete = (t) => {
    if (completingTask.value !== null) return;
    router.patch(
        route('maintenance.complete', t.id),
        {},
        {
            preserveScroll: true,
            onStart: () => (completingTask.value = t.id),
            onFinish: () => (completingTask.value = null),
        },
    );
};
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
        <div class="collection-heading">
            <p>
                <strong>{{ tasks.total }}</strong>
                tareas{{
                    Object.values(filters).some(Boolean)
                        ? ' con los filtros actuales'
                        : ' en el registro'
                }}
            </p>
            <span class="text-xs">Página {{ tasks.current_page }} de {{ tasks.last_page }}</span>
        </div>
        <div class="mb-5 flex flex-wrap gap-1 border-b border-slate-200">
            <button
                v-for="item in [
                    { v: '', l: 'Todas' },
                    { v: 'overdue', l: 'Atrasadas' },
                    { v: 'upcoming', l: 'Próximas 30 días' },
                ]"
                :key="item.v"
                class="tab-button"
                :aria-pressed="form.schedule === item.v"
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
            <input
                v-model="form.search"
                class="rounded-lg text-sm"
                placeholder="Buscar tarea…"
                aria-label="Buscar tareas"
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
                    v-for="x in ['pending', 'in_progress', 'completed', 'blocked']"
                    :key="x"
                    :value="x"
                >
                    {{ statusLabel[x] }}
                </option>
            </select>
            <select
                v-model="form.priority"
                class="rounded-lg text-sm"
                aria-label="Filtrar por prioridad"
                name="priority"
            >
                <option value="">Prioridad</option>
                <option v-for="x in ['low', 'medium', 'high']" :key="x" :value="x">
                    {{ priorityLabel[x] }}
                </option>
            </select>
            <select
                v-model="form.category"
                class="rounded-lg text-sm"
                aria-label="Filtrar por categoría"
                name="category"
            >
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
            <select
                v-model="form.website_id"
                class="rounded-lg text-sm"
                aria-label="Filtrar por web"
                name="website_id"
            >
                <option value="">Web</option>
                <option v-for="w in websites" :key="w.id" :value="w.id">{{ w.name }}</option>
            </select>
            <button class="btn-secondary">Filtrar</button>
        </form>
        <div v-if="tasks.data.length" class="table-shell">
            <div
                class="table-head hidden grid-cols-[105px_minmax(0,1.5fr)_minmax(0,1fr)_140px_95px] items-center gap-5 px-5 py-3 xl:grid"
            >
                <span>Planificación</span>
                <span>Tarea / web</span>
                <span>Responsable</span>
                <span>Prioridad / estado</span>
                <span class="text-right">Acción</span>
            </div>
            <article
                v-for="task in tasks.data"
                :key="task.id"
                class="grid gap-4 border-b border-slate-100 p-5 transition last:border-b-0 hover:bg-slate-50/70 xl:grid-cols-[105px_minmax(0,1.5fr)_minmax(0,1fr)_140px_95px] xl:items-center"
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
                    <p v-if="overdue(task)" class="mt-1 text-xs text-red-700">
                        {{ date(task.scheduled_at) }}
                    </p>
                </div>
                <div class="min-w-0">
                    <Link
                        :href="route('maintenance.show', task.id)"
                        class="font-bold text-slate-950 hover:text-teal-600"
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
                <div class="flex flex-col items-start gap-2">
                    <PriorityBadge :priority="task.priority" />
                    <StatusBadge :status="task.status" />
                </div>
                <div class="xl:text-right">
                    <button
                        v-if="canEdit(task) && task.status !== 'completed'"
                        type="button"
                        class="inline-flex min-h-9 items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800"
                        :disabled="completingTask !== null"
                        :aria-busy="completingTask === task.id"
                        @click="complete(task)"
                    >
                        Completar
                        <AppIcon name="check" :size="14" />
                    </button>
                    <Link
                        v-else
                        :href="route('maintenance.show', task.id)"
                        class="text-xs font-bold text-teal-600"
                    >
                        Ver detalle →
                    </Link>
                </div>
            </article>
        </div>
        <div v-else class="panel">
            <EmptyState
                title="Sin tareas de mantenimiento"
                icon="maintenance"
                description="Las revisiones que programes aparecerán aquí con su fecha y responsable."
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
