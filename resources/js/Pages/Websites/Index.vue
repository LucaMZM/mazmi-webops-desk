<script setup>
import { reactive } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Pagination from '@/Components/UI/Pagination.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
const props = defineProps({ websites: Object, filters: Object });
const user = usePage().props.auth.user;
const form = reactive({
    search: props.filters.search || '',
    technology: props.filters.technology || '',
    status: props.filters.status || '',
    maintenance_plan: props.filters.maintenance_plan || '',
    expiring: props.filters.expiring || '',
});
const apply = () =>
    router.get(route('websites.index'), form, { preserveState: true, replace: true });
const days = (date) => (date ? Math.ceil((new Date(date) - new Date()) / 86400000) : null);
const technologyLabel = {
    'PHP custom': 'PHP a medida',
    'Static HTML': 'HTML estático',
    Other: 'Otro',
};
const planLabel = { basic: 'Básico', standard: 'Estándar', premium: 'Premium', none: 'Sin plan' };
</script>
<template>
    <Head title="Webs" />
    <AuthenticatedLayout title="Webs">
        <PageHeader
            eyebrow="Infraestructura"
            title="Webs gestionadas"
            description="Estado técnico, planes de mantenimiento y próximos vencimientos."
        >
            <Link v-if="user.role === 'admin'" :href="route('websites.create')" class="btn-primary">
                <AppIcon name="plus" :size="17" />
                Añadir web
            </Link>
        </PageHeader>
        <form
            class="filter-bar md:grid-cols-2 xl:grid-cols-[1fr_repeat(4,160px)_auto]"
            @submit.prevent="apply"
        >
            <input
                v-model="form.search"
                class="rounded-lg text-sm"
                placeholder="Buscar nombre o URL…"
            />
            <select v-model="form.technology" class="rounded-lg text-sm">
                <option value="">Tecnología</option>
                <option
                    v-for="x in [
                        'WordPress',
                        'PrestaShop',
                        'Laravel',
                        'PHP custom',
                        'Static HTML',
                        'Other',
                    ]"
                    :key="x"
                    :value="x"
                >
                    {{ technologyLabel[x] || x }}
                </option>
            </select>
            <select v-model="form.status" class="rounded-lg text-sm">
                <option value="">Estado</option>
                <option value="stable">Estable</option>
                <option value="review">Revisión</option>
                <option value="incident">Incidencia</option>
                <option value="critical">Crítico</option>
            </select>
            <select v-model="form.maintenance_plan" class="rounded-lg text-sm">
                <option value="">Plan</option>
                <option v-for="x in ['basic', 'standard', 'premium', 'none']" :key="x" :value="x">
                    {{ planLabel[x] }}
                </option>
            </select>
            <label
                class="flex min-h-10 items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-sm"
            >
                <input
                    v-model="form.expiring"
                    true-value="1"
                    false-value=""
                    type="checkbox"
                    class="rounded text-indigo-600"
                />
                Vencen pronto
            </label>
            <button class="btn-secondary">Filtrar</button>
        </form>
        <div class="table-shell">
            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="table-cell">Web / cliente</th>
                            <th class="table-cell">Tecnología</th>
                            <th class="table-cell">Plan</th>
                            <th class="table-cell">Vencimientos</th>
                            <th class="table-cell">Estado</th>
                            <th class="table-cell"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="web in websites.data"
                            :key="web.id"
                            class="transition hover:bg-slate-50/80"
                        >
                            <td class="table-cell">
                                <p class="font-bold">{{ web.name }}</p>
                                <p class="max-w-xs truncate text-xs text-slate-500">
                                    {{ web.client.company_name }} · {{ web.url }}
                                </p>
                            </td>
                            <td class="table-cell">
                                <span
                                    class="rounded-md bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-600"
                                >
                                    {{ technologyLabel[web.technology] || web.technology }}
                                </span>
                            </td>
                            <td class="table-cell">{{ planLabel[web.maintenance_plan] }}</td>
                            <td class="table-cell text-xs">
                                <p
                                    :class="
                                        days(web.domain_expires_at) !== null &&
                                        days(web.domain_expires_at) <= 30
                                            ? 'font-bold text-amber-700'
                                            : 'text-slate-500'
                                    "
                                >
                                    Dominio:
                                    {{
                                        days(web.domain_expires_at) === null
                                            ? '—'
                                            : days(web.domain_expires_at) + ' días'
                                    }}
                                </p>
                                <p
                                    :class="
                                        days(web.hosting_expires_at) !== null &&
                                        days(web.hosting_expires_at) <= 30
                                            ? 'mt-1 font-bold text-amber-700'
                                            : 'mt-1 text-slate-500'
                                    "
                                >
                                    Hosting:
                                    {{
                                        days(web.hosting_expires_at) === null
                                            ? '—'
                                            : days(web.hosting_expires_at) + ' días'
                                    }}
                                </p>
                            </td>
                            <td class="table-cell"><StatusBadge :status="web.status" /></td>
                            <td class="table-cell text-right">
                                <Link
                                    :href="route('websites.show', web.id)"
                                    class="inline-flex min-h-9 items-center font-semibold text-indigo-600 hover:text-indigo-800"
                                >
                                    Abrir →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="grid gap-3 p-3 sm:grid-cols-2 xl:hidden">
                <Link
                    v-for="web in websites.data"
                    :key="web.id"
                    :href="route('websites.show', web.id)"
                    class="rounded-lg border border-slate-200 p-4 transition hover:border-slate-300 hover:bg-slate-50/60"
                >
                    <div class="flex justify-between gap-2">
                        <p class="font-bold">{{ web.name }}</p>
                        <StatusBadge :status="web.status" />
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ web.client.company_name }}</p>
                    <div class="mt-4 flex items-center justify-between text-xs">
                        <span class="rounded-lg bg-slate-100 px-2 py-1 font-semibold">
                            {{ technologyLabel[web.technology] || web.technology }}
                        </span>
                        <span class="text-slate-500">{{ planLabel[web.maintenance_plan] }}</span>
                    </div>
                    <p
                        v-if="
                            (days(web.domain_expires_at) !== null &&
                                days(web.domain_expires_at) <= 30) ||
                            (days(web.hosting_expires_at) !== null &&
                                days(web.hosting_expires_at) <= 30)
                        "
                        class="mt-3 text-xs font-bold text-amber-700"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            <AppIcon name="alert" :size="14" />
                            Hay un vencimiento próximo
                        </span>
                    </p>
                </Link>
            </div>
            <EmptyState v-if="!websites.data.length" title="No hay webs con estos filtros" />
            <Pagination :links="websites.links" />
        </div>
    </AuthenticatedLayout>
</template>
