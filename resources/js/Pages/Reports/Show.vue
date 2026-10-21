<script setup>
import BrandMark from '@/Components/UI/BrandMark.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import { Head, usePage } from '@inertiajs/vue3';
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
        <div class="print:hidden">
            <PageHeader
                eyebrow="Reportes"
                title="Informe mensual"
                :description="report.client.company_name"
                :back-href="route('reports.index')"
            >
                <button type="button" class="btn-primary" @click="printReport">
                    <AppIcon name="print" :size="17" />
                    Imprimir reporte
                </button>
                <ConfirmDeleteModal
                    v-if="user.role === 'admin'"
                    :url="route('reports.destroy', report.id)"
                />
            </PageHeader>
        </div>
        <article
            class="mx-auto max-w-[1000px] overflow-hidden rounded-xl border border-slate-200 bg-white print:border-0"
        >
            <div class="h-1.5 bg-indigo-600" />
            <header class="px-6 pb-7 pt-8 sm:px-10 sm:pt-10">
                <div class="flex flex-wrap items-start justify-between gap-5">
                    <div class="flex items-center gap-3">
                        <BrandMark class="h-9 w-9 text-indigo-600" />
                        <div>
                            <p class="font-display text-sm font-bold" translate="no">
                                Mazmi WebOps Desk
                            </p>
                            <p class="mt-0.5 text-xs text-slate-500">Informe de servicio</p>
                        </div>
                    </div>
                    <p class="text-xs tabular-nums text-slate-500">
                        Ref. {{ report.year }} / {{ String(report.month).padStart(2, '0') }} /
                        {{ String(report.id).padStart(3, '0') }}
                    </p>
                </div>
                <div class="mt-10 flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                    <div>
                        <h2
                            class="text-3xl font-semibold leading-tight tracking-[-0.045em] sm:text-4xl"
                        >
                            {{ report.client.company_name }}
                        </h2>
                        <p class="mt-3 text-base text-slate-500">
                            Mantenimiento web · {{ months[report.month] }} {{ report.year }}
                        </p>
                    </div>
                    <StatusBadge :status="report.general_status" />
                </div>
            </header>
            <div class="mx-6 border-t border-slate-200 sm:mx-10" />
            <div class="space-y-9 px-6 py-8 sm:px-10 sm:py-9">
                <section class="grid gap-4 sm:grid-cols-[170px_minmax(0,1fr)]">
                    <h3 class="text-sm font-bold">Resumen del periodo</h3>
                    <p class="max-w-[65ch] whitespace-pre-line text-base leading-8 text-slate-700">
                        {{ report.summary }}
                    </p>
                </section>
                <section
                    class="rounded-lg bg-slate-50 px-5 py-6 sm:px-7"
                    aria-label="Actividad registrada"
                >
                    <h3 class="mb-6 text-xs font-medium text-slate-500">
                        Actividad registrada en el periodo
                    </h3>
                    <dl class="grid gap-5 sm:grid-cols-3 sm:divide-x sm:divide-slate-200">
                        <div>
                            <dd class="stat-number text-4xl">{{ report.completed_tasks_count }}</dd>
                            <dt class="mt-2 text-sm text-slate-600">Tareas completadas</dt>
                        </div>
                        <div class="sm:pl-6">
                            <dd class="stat-number text-4xl">
                                {{ report.resolved_tickets_count }}
                            </dd>
                            <dt class="mt-2 text-sm text-slate-600">Tickets resueltos</dt>
                        </div>
                        <div class="sm:pl-6">
                            <dd class="stat-number text-4xl">{{ report.pending_tickets_count }}</dd>
                            <dt class="mt-2 text-sm text-slate-600">Tickets pendientes</dt>
                        </div>
                    </dl>
                </section>
                <section
                    class="grid gap-4 border-t border-slate-200 pt-8 sm:grid-cols-[170px_minmax(0,1fr)]"
                >
                    <h3 class="text-sm font-bold">Recomendaciones</h3>
                    <div>
                        <p
                            class="max-w-[65ch] whitespace-pre-line text-base leading-8 text-slate-700"
                        >
                            {{ report.recommendations }}
                        </p>
                        <p
                            class="mt-5 border-l-2 border-indigo-300 pl-4 text-xs leading-6 text-slate-500"
                        >
                            Estas recomendaciones orientan la planificación del siguiente periodo.
                        </p>
                    </div>
                </section>
                <footer
                    class="flex flex-col justify-between gap-2 border-t border-slate-200 pt-6 text-xs text-slate-500 sm:flex-row"
                >
                    <span>{{ months[report.month] }} {{ report.year }}</span>
                    <span>Emitido desde Mazmi WebOps Desk</span>
                </footer>
            </div>
        </article>
    </AuthenticatedLayout>
</template>
