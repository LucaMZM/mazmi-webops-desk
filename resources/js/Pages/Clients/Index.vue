<script setup>
import { reactive } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Pagination from '@/Components/UI/Pagination.vue';
const props = defineProps({ clients: Object, filters: Object });
const form = reactive({ search: props.filters.search || '', status: props.filters.status || '' });
const apply = () =>
    router.get(route('clients.index'), form, { preserveState: true, replace: true });
const user = usePage().props.auth.user;
</script>
<template>
    <Head title="Clientes" />
    <AuthenticatedLayout title="Clientes">
        <PageHeader
            eyebrow="Cartera"
            title="Clientes"
            description="Empresas, contactos y contexto operativo en un único lugar."
        >
            <Link v-if="user.role === 'admin'" :href="route('clients.create')" class="btn-primary">
                + Nuevo cliente
            </Link>
        </PageHeader>
        <form
            class="panel mb-5 grid gap-3 p-4 sm:grid-cols-[1fr_190px_auto]"
            @submit.prevent="apply"
        >
            <input
                v-model="form.search"
                class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Buscar empresa, contacto o ciudad…"
            />
            <select
                v-model="form.status"
                class="rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
            <button class="btn-secondary">Aplicar filtros</button>
        </form>
        <div class="table-shell">
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="table-cell">Empresa</th>
                            <th class="table-cell">Contacto</th>
                            <th class="table-cell">Ubicación</th>
                            <th class="table-cell">Actividad</th>
                            <th class="table-cell">Estado</th>
                            <th class="table-cell"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="client in clients.data"
                            :key="client.id"
                            class="hover:bg-slate-50"
                        >
                            <td class="table-cell">
                                <p class="font-bold text-slate-900">{{ client.company_name }}</p>
                                <p class="text-xs text-slate-500">{{ client.email }}</p>
                            </td>
                            <td class="table-cell">{{ client.contact_name }}</td>
                            <td class="table-cell text-slate-500">{{ client.city || '—' }}</td>
                            <td class="table-cell text-xs text-slate-500">
                                {{ client.websites_count }} webs ·
                                {{ client.tickets_count }} tickets
                            </td>
                            <td class="table-cell"><StatusBadge :status="client.status" /></td>
                            <td class="table-cell text-right">
                                <Link
                                    :href="route('clients.show', client.id)"
                                    class="font-semibold text-indigo-600"
                                >
                                    Ver →
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="divide-y divide-slate-100 md:hidden">
                <Link
                    v-for="client in clients.data"
                    :key="client.id"
                    :href="route('clients.show', client.id)"
                    class="block p-4"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold">{{ client.company_name }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ client.contact_name }} · {{ client.city || 'Sin ciudad' }}
                            </p>
                        </div>
                        <StatusBadge :status="client.status" />
                    </div>
                    <p class="mt-3 text-xs font-medium text-slate-400">
                        {{ client.websites_count }} webs · {{ client.tickets_count }} tickets
                    </p>
                </Link>
            </div>
            <EmptyState
                v-if="!clients.data.length"
                title="No hay clientes con estos filtros"
                description="Ajusta la búsqueda o crea el primer cliente."
            >
                <Link
                    v-if="user.role === 'admin'"
                    :href="route('clients.create')"
                    class="btn-primary"
                >
                    Crear cliente
                </Link>
            </EmptyState>
            <Pagination :links="clients.links" />
        </div>
    </AuthenticatedLayout>
</template>
