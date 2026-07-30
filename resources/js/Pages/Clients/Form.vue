<script setup>
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
    'w-full rounded-xl border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500';
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
        <form class="space-y-6" @submit.prevent="submit">
            <section class="panel p-5 sm:p-6">
                <h3 class="font-bold">Datos de empresa</h3>
                <p class="mb-5 text-sm text-slate-500">Identificación y persona de contacto.</p>
                <div class="grid gap-5 md:grid-cols-2">
                    <FormField label="Empresa" required :error="form.errors.company_name">
                        <input v-model="form.company_name" :class="input" maxlength="150" />
                    </FormField>
                    <FormField
                        label="Persona de contacto"
                        required
                        :error="form.errors.contact_name"
                    >
                        <input v-model="form.contact_name" :class="input" maxlength="120" />
                    </FormField>
                    <FormField label="Email" required :error="form.errors.email">
                        <input v-model="form.email" type="email" :class="input" />
                    </FormField>
                    <FormField label="Teléfono" :error="form.errors.phone">
                        <input v-model="form.phone" :class="input" />
                    </FormField>
                    <FormField label="Ciudad" :error="form.errors.city">
                        <input v-model="form.city" :class="input" />
                    </FormField>
                    <FormField label="Estado" required :error="form.errors.status">
                        <select v-model="form.status" :class="input">
                            <option value="active">Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </FormField>
                </div>
            </section>
            <section class="panel p-5 sm:p-6">
                <FormField
                    label="Notas operativas"
                    :error="form.errors.notes"
                    hint="No guardes contraseñas ni credenciales reales."
                >
                    <textarea
                        v-model="form.notes"
                        rows="5"
                        :class="input"
                        maxlength="3000"
                        placeholder="Preferencias, contexto de servicio, ventanas de mantenimiento…"
                    />
                </FormField>
            </section>
            <div class="flex justify-end gap-3">
                <Link
                    :href="editing ? route('clients.show', client.id) : route('clients.index')"
                    class="btn-secondary"
                >
                    Cancelar
                </Link>
                <button class="btn-primary" :disabled="form.processing">
                    {{
                        form.processing
                            ? 'Guardando…'
                            : editing
                              ? 'Guardar cambios'
                              : 'Crear cliente'
                    }}
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
