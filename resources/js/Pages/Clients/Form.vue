<script setup>
import SubmitButton from '@/Components/UI/SubmitButton.vue';
import { useFormFeedback } from '@/Composables/useFormFeedback';
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import FormField from '@/Components/UI/FormField.vue';
const props = defineProps({ client: Object });
const editing = computed(() => !!props.client);
const form = useForm({
    company_name: props.client?.company_name || '',
    contact_name: props.client?.contact_name || '',
    email: props.client?.email || '',
    phone: props.client?.phone || '',
    city: props.client?.city || '',
    status: props.client?.status || 'active',
    notes: props.client?.notes || '',
});
const submit = () =>
    editing.value
        ? form.put(route('clients.update', props.client.id))
        : form.post(route('clients.store'));
const input =
    'w-full rounded-lg border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500';
const { formElement } = useFormFeedback(form);
</script>
<template>
    <Head :title="editing ? 'Editar cliente' : 'Nuevo cliente'" />
    <AuthenticatedLayout :title="editing ? 'Editar cliente' : 'Nuevo cliente'">
        <PageHeader
            eyebrow="Clientes"
            :title="editing ? 'Editar ficha' : 'Crear cliente'"
            description="Información comercial y de contacto. No incluyas credenciales ni datos sensibles."
            :back-href="editing ? route('clients.show', client.id) : route('clients.index')"
        />
        <form
            ref="formElement"
            :aria-busy="form.processing"
            class="editor-form glass-editor max-w-5xl rounded-[24px] border border-white/70 bg-white/40"
            @submit.prevent="submit"
        >
            <section class="form-section">
                <h2 class="section-heading">Datos de empresa</h2>
                <p class="mb-5 text-sm text-slate-500">Identificación y persona de contacto.</p>
                <div class="grid gap-5 md:grid-cols-2">
                    <FormField label="Empresa" required :error="form.errors.company_name">
                        <input
                            v-model="form.company_name"
                            name="company_name"
                            :class="input"
                            maxlength="150"
                        />
                    </FormField>
                    <FormField
                        label="Persona de contacto"
                        required
                        :error="form.errors.contact_name"
                    >
                        <input
                            v-model="form.contact_name"
                            name="contact_name"
                            :class="input"
                            maxlength="120"
                        />
                    </FormField>
                    <FormField label="Email" required :error="form.errors.email">
                        <input
                            v-model="form.email"
                            name="email"
                            spellcheck="false"
                            autocomplete="email"
                            type="email"
                            :class="input"
                        />
                    </FormField>
                    <FormField label="Teléfono" :error="form.errors.phone">
                        <input
                            type="tel"
                            autocomplete="tel"
                            v-model="form.phone"
                            name="phone"
                            :class="input"
                        />
                    </FormField>
                    <FormField label="Ciudad" :error="form.errors.city">
                        <input v-model="form.city" name="city" :class="input" />
                    </FormField>
                    <FormField label="Estado" required :error="form.errors.status">
                        <select v-model="form.status" name="status" :class="input">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </FormField>
                </div>
            </section>
            <section class="form-section">
                <FormField
                    label="Notas operativas"
                    :error="form.errors.notes"
                    hint="No guardes contraseñas ni credenciales reales."
                >
                    <textarea
                        v-model="form.notes"
                        name="notes"
                        rows="5"
                        :class="input"
                        maxlength="3000"
                        placeholder="Preferencias, contexto de servicio, ventanas de mantenimiento…"
                    />
                </FormField>
            </section>
            <div class="form-footer">
                <Link
                    :href="editing ? route('clients.show', client.id) : route('clients.index')"
                    class="btn-secondary"
                >
                    Cancelar
                </Link>
                <SubmitButton :processing="form.processing">
                    {{
                        form.processing
                            ? 'Guardando…'
                            : editing
                              ? 'Guardar cambios'
                              : 'Crear cliente'
                    }}
                </SubmitButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
