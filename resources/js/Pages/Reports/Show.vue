<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import ConfirmDeleteModal from '@/Components/UI/ConfirmDeleteModal.vue';
defineProps({ report: Object });
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
const printReport = () => window.print();
</script>
<template>
    <Head :title="`Reporte ${months[report.month]} ${report.year}`" />
    <AuthenticatedLayout title="Reporte mensual">
        <PageHeader
            eyebrow="Informe de servicio"
            :title="`${months[report.month]} ${report.year}`"
            :description="report.client.company_name"
            :back-href="route('reports.index')"
        >
            <button class="btn-secondary print:hidden" @click="printReport">Imprimir</button>
            <ConfirmDeleteModal
                v-if="user.role === 'admin'"
                :url="route('reports.destroy', report.id)"
            />
        </PageHeader>
        <article
            class="mx-auto max-w-5xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
        >
            <header class="bg-slate-950 p-6 text-white sm:p-10">
                <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[.25em] text-indigo-300">
                            Mazmi WebOps Desk · Reporte mensual
                        </p>
                        <h2 class="mt-4 text-3xl font-black sm:text-4xl">
                            {{ report.client.company_name }}
                        </h2>
                        <p class="mt-2 text-slate-300">
                            {{ months[report.month] }} de {{ report.year }}
                        </p>
                    </div>
                    <StatusBadge :status="report.general_status" />
                </div>
            </header>
            <div class="p-6 sm:p-10">
                <section>
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                        Resumen ejecutivo
                    </p>
                    <p class="mt-4 whitespace-pre-line text-lg leading-8 text-slate-700">
                        {{ report.summary }}
                    </p>
                </section>
                <section class="my-9 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-indigo-50 p-5">
                        <p class="text-3xl font-black text-indigo-700">
                            {{ report.completed_tasks_count }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-indigo-900">Tareas completadas</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-5">
                        <p class="text-3xl font-black text-emerald-700">
                            {{ report.resolved_tickets_count }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-emerald-900">Tickets resueltos</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50 p-5">
                        <p class="text-3xl font-black text-amber-700">
                            {{ report.pending_tickets_count }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-amber-900">Tickets pendientes</p>
                    </div>
                </section>
                <section class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                        Recomendaciones
                    </p>
                    <p class="mt-4 whitespace-pre-line leading-7 text-slate-700">
                        {{ report.recommendations }}
                    </p>
                </section>
                <footer
                    class="mt-10 flex flex-col justify-between gap-3 border-t border-slate-100 pt-6 text-xs text-slate-400 sm:flex-row"
                >
                    <span>Generado desde Mazmi WebOps Desk</span>
                    <span>Panel de mantenimiento web · Luca Mazmishvili</span>
                </footer>
            </div>
        </article>
    </AuthenticatedLayout>
</template>
