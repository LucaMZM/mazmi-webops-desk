<script setup>
import SubmitButton from '@/Components/UI/SubmitButton.vue';
import { useFormFeedback } from '@/Composables/useFormFeedback';
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import FormField from '@/Components/UI/FormField.vue';
const props = defineProps({ task: Object, websites: Array, technicians: Array });
const user = usePage().props.auth.user;
const editing = computed(() => !!props.task);
const query = route().params;
const localDate = (v) => (v ? new Date(v).toISOString().slice(0, 16) : '');
const form = useForm({
    website_id: props.task?.website_id || Number(query.website_id) || props.websites[0]?.id || '',
    assigned_to: props.task?.assigned_to || '',
    title: props.task?.title || '',
    description: props.task?.description || '',
    category: props.task?.category || 'updates',
    priority: props.task?.priority || 'medium',
    status: props.task?.status || 'pending',
    scheduled_at: localDate(props.task?.scheduled_at),
});
const submit = () =>
    editing.value
        ? form.put(route('maintenance.update', props.task.id))
        : form.post(route('maintenance.store'));
const input =
    'w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500';
const categoryLabel = {
    backups: 'Copias de seguridad',
    updates: 'Actualizaciones',
    security: 'Seguridad',
    performance: 'Rendimiento',
    content: 'Contenido',
    seo: 'SEO',
    other: 'Otro',
};
const { formElement } = useFormFeedback(form);
</script>
<template>
    <Head :title="editing ? 'Editar tarea' : 'Nueva tarea'" />
    <AuthenticatedLayout :title="editing ? 'Editar tarea' : 'Nueva tarea'">
        <PageHeader
            eyebrow="Mantenimiento"
            :title="editing ? 'Editar tarea' : 'Programar mantenimiento'"
            description="Define un trabajo concreto, asignable y verificable."
            :back-href="editing ? route('maintenance.show', task.id) : route('maintenance.index')"
        />
        <div v-if="!editing && !websites.length" class="glass-hero mb-6 p-6 sm:p-8">
            <p class="webops-eyebrow">PRIMER PASO</p>
            <h2 class="mt-3 text-xl font-semibold tracking-tight text-slate-900">
                Prepara tu espacio antes de continuar
            </h2>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Primero registra una web para poder programar su mantenimiento.
            </p>
            <Link :href="route('websites.create')" class="btn-primary mt-5">Registrar web</Link>
        </div>
        <form
            v-if="editing || websites.length"
            ref="formElement"
            :aria-busy="form.processing"
            class="editor-form glass-editor max-w-5xl rounded-[24px] border border-white/70 bg-white/40"
            @submit.prevent="submit"
        >
            <section class="form-section">
                <h2 class="section-heading">Trabajo a realizar</h2>
                <div class="grid gap-5 md:grid-cols-2">
                    <FormField label="Web" required :error="form.errors.website_id">
                        <select
                            v-model="form.website_id"
                            name="website_id"
                            :disabled="user.role === 'technician'"
                            :class="input"
                        >
                            <option v-for="w in websites" :key="w.id" :value="w.id">
                                {{ w.name }} · {{ w.client.company_name }}
                            </option>
                        </select>
                    </FormField>
                    <FormField label="Categoría" required :error="form.errors.category">
                        <select v-model="form.category" name="category" :class="input">
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
                    </FormField>
                    <FormField
                        label="Título"
                        required
                        :error="form.errors.title"
                        class="md:col-span-2"
                    >
                        <input v-model="form.title" name="title" :class="input" maxlength="180" />
                    </FormField>
                    <FormField
                        label="Descripción / checklist"
                        :error="form.errors.description"
                        class="md:col-span-2"
                    >
                        <textarea
                            v-model="form.description"
                            name="description"
                            rows="5"
                            :class="input"
                            maxlength="5000"
                        />
                    </FormField>
                </div>
            </section>
            <section class="form-section">
                <h2 class="section-heading">Planificación</h2>
                <div class="grid gap-5 md:grid-cols-2">
                    <FormField label="Técnico" :error="form.errors.assigned_to">
                        <select
                            v-model="form.assigned_to"
                            name="assigned_to"
                            :disabled="user.role === 'technician'"
                            :class="input"
                        >
                            <option value="">Sin asignar</option>
                            <option v-for="t in technicians" :key="t.id" :value="t.id">
                                {{ t.name }}
                            </option>
                        </select>
                    </FormField>
                    <FormField label="Fecha programada" :error="form.errors.scheduled_at">
                        <input
                            v-model="form.scheduled_at"
                            name="scheduled_at"
                            type="datetime-local"
                            :class="input"
                        />
                    </FormField>
                    <FormField label="Prioridad" required :error="form.errors.priority">
                        <select v-model="form.priority" name="priority" :class="input">
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                        </select>
                    </FormField>
                    <FormField label="Estado" required :error="form.errors.status">
                        <select v-model="form.status" name="status" :class="input">
                            <option value="pending">Pendiente</option>
                            <option value="in_progress">En curso</option>
                            <option value="completed">Completada</option>
                            <option value="blocked">Bloqueada</option>
                        </select>
                    </FormField>
                </div>
            </section>
            <div class="form-footer">
                <Link
                    :href="
                        editing ? route('maintenance.show', task.id) : route('maintenance.index')
                    "
                    class="btn-secondary"
                >
                    Cancelar
                </Link>
                <SubmitButton :processing="form.processing">
                    {{ form.processing ? 'Guardando…' : 'Guardar tarea' }}
                </SubmitButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
