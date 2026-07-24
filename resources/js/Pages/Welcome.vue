<script setup>
import { Head, Link } from '@inertiajs/vue3';

const previewStats = [
    { value: '8', label: 'Clientes demo', color: 'text-indigo-600' },
    { value: '12', label: 'Webs registradas', color: 'text-blue-600' },
    { value: '10', label: 'Tickets abiertos', color: 'text-orange-600' },
    { value: '18', label: 'Tareas pendientes', color: 'text-emerald-600' },
];

const previewTasks = [
    'Revisar actualizaciones de una web WordPress',
    'Comprobar un certificado SSL próximo a vencer',
    'Preparar el resumen mensual de un cliente',
];

const benefits = [
    {
        title: 'Contexto en un solo sitio',
        description: 'Clientes, webs, tickets y tareas conectados para saber qué pasa y quién lo lleva.',
    },
    {
        title: 'Trabajo técnico ordenado',
        description: 'Prioridades, estados, vencimientos y asignaciones pensados para mantenimiento web real.',
    },
    {
        title: 'Información clara para clientes',
        description: 'Acceso limitado a sus datos y reportes mensuales fáciles de revisar o imprimir.',
    },
];

const stack = ['Laravel 13', 'Vue 3', 'Inertia.js', 'Tailwind CSS', 'MySQL', 'Vite'];
</script>

<template>
    <Head title="Panel de mantenimiento web" />

    <div class="min-h-screen bg-slate-950 text-white">
        <header class="mx-auto flex max-w-7xl items-center justify-between px-5 py-6 sm:px-8">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-500 font-black">
                    M
                </div>
                <div>
                    <p class="font-bold">Mazmi WebOps Desk</p>
                    <p class="text-[10px] uppercase tracking-widest text-slate-400">
                        Operaciones web
                    </p>
                </div>
            </div>

            <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="btn-primary">
                Ir al dashboard
            </Link>
            <Link
                v-else
                :href="route('login')"
                class="rounded-xl border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10"
            >
                Iniciar sesión
            </Link>
        </header>

        <main>
            <section class="relative overflow-hidden">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(79,70,229,.28),transparent_40%)]" />

                <div class="relative mx-auto grid max-w-7xl gap-14 px-5 pb-20 pt-14 sm:px-8 lg:grid-cols-2 lg:items-center lg:pb-28 lg:pt-24">
                    <div>
                        <div class="inline-flex rounded-full border border-indigo-400/30 bg-indigo-400/10 px-3 py-1 text-xs font-semibold text-indigo-200">
                            Laravel · Vue · Inertia
                        </div>
                        <h1 class="mt-7 text-4xl font-black leading-tight tracking-tight sm:text-6xl">
                            Mantenimiento web organizado,
                            <span class="text-indigo-400">sin perder contexto.</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">
                            Mazmi WebOps Desk ayuda a seguir clientes, webs, tickets, tareas y reportes mensuales desde un panel único. Está pensado para el día a día de una pequeña agencia o freelancer técnico.
                        </p>
                        <div class="mt-9 flex flex-wrap gap-3">
                            <Link :href="route('login')" class="btn-primary px-6 py-3">
                                Probar demo →
                            </Link>
                            <a href="#proyecto" class="rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold hover:bg-white/10">
                                Ver qué incluye
                            </a>
                        </div>
                        <p class="mt-4 text-xs text-slate-500">
                            Acceso demo: admin@webops.test · password
                        </p>
                    </div>

                    <div class="relative">
                        <div class="rounded-3xl border border-white/10 bg-white/5 p-4 shadow-2xl backdrop-blur">
                            <div class="rounded-2xl bg-slate-50 p-5 text-slate-900">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">
                                            Vista demo
                                        </p>
                                        <p class="mt-1 text-xl font-black">
                                            Panel de ejemplo
                                        </p>
                                    </div>
                                    <div class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">
                                        Datos ficticios
                                    </div>
                                </div>

                                <div class="mt-5 grid grid-cols-2 gap-3">
                                    <div
                                        v-for="item in previewStats"
                                        :key="item.label"
                                        class="rounded-xl border border-slate-200 p-4"
                                    >
                                        <p :class="['text-3xl font-black', item.color]">
                                            {{ item.value }}
                                        </p>
                                        <p class="text-xs text-slate-500">
                                            {{ item.label }}
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-2">
                                    <div
                                        v-for="(label, index) in previewTasks"
                                        :key="label"
                                        class="flex items-center gap-3 rounded-xl bg-slate-100 p-3 text-xs font-semibold"
                                    >
                                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-white text-indigo-600">
                                            {{ index + 1 }}
                                        </span>
                                        {{ label }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="proyecto" class="border-y border-white/10 bg-white/[.03]">
                <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
                    <div class="max-w-2xl">
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">
                            Operaciones centralizadas
                        </p>
                        <h2 class="mt-3 text-3xl font-black">
                            Un flujo claro para mantenimiento web
                        </h2>
                        <p class="mt-4 leading-7 text-slate-400">
                            La aplicación cubre soporte, inventario técnico, tareas programadas, vencimientos y reportes. Cada rol ve únicamente la información que necesita.
                        </p>
                    </div>

                    <div class="mt-10 grid gap-5 md:grid-cols-3">
                        <article
                            v-for="(item, index) in benefits"
                            :key="item.title"
                            class="rounded-2xl border border-white/10 bg-slate-900 p-6"
                        >
                            <span class="text-sm font-black text-indigo-400">
                                0{{ index + 1 }}
                            </span>
                            <h3 class="mt-5 text-lg font-bold">
                                {{ item.title }}
                            </h3>
                            <p class="mt-2 text-sm leading-6 text-slate-400">
                                {{ item.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section class="mx-auto max-w-7xl px-5 py-20 sm:px-8">
                <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-indigo-400">
                            Stack técnico
                        </p>
                        <h2 class="mt-3 text-3xl font-black">
                            Laravel y Vue trabajando juntos
                        </h2>
                        <p class="mt-4 leading-7 text-slate-400">
                            Laravel gestiona rutas, validación, autorización y datos. Vue 3 construye la interfaz, Inertia conecta ambas partes sin una API separada y Tailwind mantiene una base visual consistente.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <div
                            v-for="tech in stack"
                            :key="tech"
                            class="rounded-xl border border-white/10 bg-white/5 p-4 text-center text-sm font-bold"
                        >
                            {{ tech }}
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 px-5 py-8 text-center text-xs text-slate-500">
            Diseñado y desarrollado por Luca Mazmishvili · Valencia, España
        </footer>
    </div>
</template>
