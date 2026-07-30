<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import FormField from '@/Components/UI/FormField.vue';
defineProps({ clients: Array });
const now = new Date();
const form = useForm({
    client_id: '',
    month: now.getMonth() || 12,
    year: now.getMonth() ? now.getFullYear() : now.getFullYear() - 1,
    summary: '',
    completed_tasks_count: 0,
    resolved_tickets_count: 0,
    pending_tickets_count: 0,
    recommendations: '',
    general_status: 'good',
});
const input =
    'w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500';
const submit = () => form.post(route('reports.store'));
const months = [
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
</script>
<template>
    <Head title="Crear reporte" />
    <AuthenticatedLayout title="Crear reporte">
        <PageHeader
            eyebrow="Reportes"
            title="Nuevo reporte mensual"
            description="Prepara un resumen claro que pueda compartirse directamente con el cliente."
            :back-href="route('reports.index')"
        />
        <form class="space-y-6" @submit.prevent="submit">
            <section class="panel p-5 sm:p-6">
                <h3 class="font-bold">Periodo y cliente</h3>
                <div class="mt-5 grid gap-5 md:grid-cols-3">
                    <FormField label="Cliente" required :error="form.errors.client_id">
                        <select v-model="form.client_id" :class="input">
                            <option value="" disabled>Selecciona un cliente</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">
                                {{ c.company_name }}
                            </option>
                        </select>
                    </FormField>
                    <FormField label="Mes" required :error="form.errors.month">
                        <select v-model="form.month" :class="input">
                            <option v-for="(m, i) in months" :key="m" :value="i + 1">
                                {{ m }}
                            </option>
                        </select>
                    </FormField>
                    <FormField label="Año" required :error="form.errors.year">
                        <input
                            v-model="form.year"
                            type="number"
                            min="2020"
                            max="2100"
                            :class="input"
                        />
                    </FormField>
                </div>
            </section>
            <section class="panel p-5 sm:p-6">
                <h3 class="font-bold">Resumen ejecutivo</h3>
                <div class="mt-5 grid gap-5">
                    <FormField label="Resumen del mes" required :error="form.errors.summary">
                        <textarea
                            v-model="form.summary"
                            rows="6"
                            :class="input"
                            maxlength="5000"
                            placeholder="Actividad realizada, contexto y resultado general…"
                        />
                    </FormField>
                    <FormField
                        label="Recomendaciones"
                        required
                        :error="form.errors.recommendations"
                    >
                        <textarea
                            v-model="form.recommendations"
                            rows="5"
                            :class="input"
                            maxlength="5000"
                            placeholder="Próximos pasos concretos y priorizados…"
                        />
                    </FormField>
                </div>
            </section>
            <section class="panel p-5 sm:p-6">
                <h3 class="font-bold">Indicadores</h3>
                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <FormField
                        label="Tareas completadas"
                        required
                        :error="form.errors.completed_tasks_count"
                    >
                        <input
                            v-model="form.completed_tasks_count"
                            type="number"
                            min="0"
                            :class="input"
                        />
                    </FormField>
                    <FormField
                        label="Tickets resueltos"
                        required
                        :error="form.errors.resolved_tickets_count"
                    >
                        <input
                            v-model="form.resolved_tickets_count"
                            type="number"
                            min="0"
                            :class="input"
                        />
                    </FormField>
                    <FormField
                        label="Tickets pendientes"
                        required
                        :error="form.errors.pending_tickets_count"
                    >
                        <input
                            v-model="form.pending_tickets_count"
                            type="number"
                            min="0"
                            :class="input"
                        />
                    </FormField>
                    <FormField label="Estado general" required :error="form.errors.general_status">
                        <select v-model="form.general_status" :class="input">
                            <option value="good">Correcto</option>
                            <option value="attention">Requiere atención</option>
                            <option value="critical">Crítico</option>
                        </select>
                    </FormField>
                </div>
            </section>
            <div class="flex justify-end gap-3">
                <Link :href="route('reports.index')" class="btn-secondary">Cancelar</Link>
                <button class="btn-primary" :disabled="form.processing">
                    {{ form.processing ? 'Generando…' : 'Crear reporte' }}
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
