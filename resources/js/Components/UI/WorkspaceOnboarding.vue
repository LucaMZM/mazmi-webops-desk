<script setup>
import { Link } from '@inertiajs/vue3';
import AppIcon from '@/Components/UI/AppIcon.vue';
defineProps({ clients: { type: Number, default: 0 }, websites: { type: Number, default: 0 } });
const steps = [
    { title: 'Añade tu primer cliente', description: 'Crea una ficha real con sus datos de contacto.', icon: 'clients', route: 'clients.create' },
    { title: 'Registra su página web', description: 'Guarda hosting, tecnología, dominio y fechas importantes.', icon: 'websites', route: 'websites.create' },
    { title: 'Organiza el mantenimiento', description: 'Abre tickets, programa tareas y controla sus resultados.', icon: 'maintenance', route: 'maintenance.index' },
];
</script>

<template>
    <section class="webops-onboarding glass-hero mb-8" aria-labelledby="first-steps-title" data-glass-optic>
        <div class="relative z-[1]">
            <p class="webops-eyebrow">TU ESPACIO ESTÁ LISTO</p>
            <div class="mt-5 grid gap-5 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
                <div>
                    <h2 id="first-steps-title" class="max-w-[25ch] font-display text-3xl font-semibold leading-[1.12] tracking-[-0.055em] text-slate-900 sm:text-4xl">
                        Todo empieza con tu primer cliente.
                    </h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-slate-600">
                        Sin ejemplos ni cifras inventadas. Este espacio se irá construyendo con tu actividad real.
                    </p>
                </div>
                <Link :href="route('clients.create')" class="btn-primary max-w-fit">
                    <AppIcon name="plus" :size="17" /> Crear primer cliente
                </Link>
            </div>
            <div class="mt-9 grid gap-3 md:grid-cols-3">
                <div v-for="(step, index) in steps" :key="step.route" class="webops-step">
                    <div class="flex items-center justify-between">
                        <span class="webops-step-index">{{ String(index + 1).padStart(2, '0') }}</span>
                        <AppIcon :name="step.icon" :size="19" class="text-teal-700" />
                    </div>
                    <h3 class="mt-5 text-sm font-semibold text-slate-900">{{ step.title }}</h3>
                    <p class="mt-2 text-xs leading-6 text-slate-600">{{ step.description }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
