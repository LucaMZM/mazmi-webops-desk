<script setup>
import { Head, Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/UI/BrandMark.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';

const areas = [
    {
        title: 'El inventario, con contexto',
        text: 'Web, cliente, tecnología, hosting y vencimientos en una misma ficha. Sin tener que reconstruir la información cada vez.',
        icon: 'websites',
    },
    {
        title: 'El soporte, con responsable',
        text: 'Tickets con prioridad, estado y técnico asignado. Un punto de partida claro para atender cada solicitud.',
        icon: 'tickets',
    },
    {
        title: 'El mantenimiento, con seguimiento',
        text: 'Tareas programadas, trabajo pendiente y reportes mensuales para explicar qué se ha hecho y qué queda por hacer.',
        icon: 'maintenance',
    },
];
const stack = ['Laravel', 'Vue 3', 'Inertia.js', 'Tailwind CSS', 'MySQL'];
</script>
<template>
    <Head title="Mantenimiento web, con contexto" />
    <div class="bg-white text-slate-900">
        <a href="#content" class="skip-link">Ir al contenido</a>
        <header class="border-b border-slate-200">
            <div
                class="mx-auto flex min-h-[88px] max-w-[1320px] items-center justify-between gap-5 px-5 sm:px-8"
            >
                <Link href="/" class="flex items-center gap-3" title="Mazmi WebOps Desk, inicio">
                    <BrandMark class="h-10 w-10 shrink-0 text-indigo-600" />
                    <div translate="no">
                        <p class="font-display text-lg font-extrabold tracking-tight">Mazmi</p>
                        <p class="-mt-0.5 text-xs text-slate-500">WebOps Desk</p>
                    </div>
                </Link>
                <div class="flex items-center gap-7">
                    <a
                        href="#proyecto"
                        class="hidden text-sm text-slate-600 hover:text-indigo-700 sm:block"
                    >
                        El espacio de trabajo
                    </a>
                    <Link
                        :href="$page.props.auth.user ? route('dashboard') : route('login')"
                        class="btn-secondary"
                    >
                        {{ $page.props.auth.user ? 'Abrir panel' : 'Iniciar sesión' }}
                        <AppIcon name="arrow-right" :size="16" />
                    </Link>
                </div>
            </div>
        </header>
        <main id="content">
            <section
                class="mx-auto grid max-w-[1320px] items-center gap-12 px-5 pb-16 pt-14 sm:px-8 sm:py-20 lg:grid-cols-[1.1fr_1fr] lg:gap-16 lg:py-24"
            >
                <div>
                    <p class="mb-6 flex items-center gap-2 text-sm font-medium text-slate-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-600" />
                        Operaciones web para equipos pequeños
                    </p>
                    <h1
                        class="max-w-[12ch] text-[clamp(2.6rem,5vw,4.6rem)] font-semibold leading-[1.09] tracking-[-0.065em]"
                    >
                        Todo lo que hay detrás de una web, a la vista.
                    </h1>
                    <p
                        class="mt-7 max-w-[48ch] text-base leading-7 text-slate-600 sm:text-lg sm:leading-8"
                    >
                        Organiza clientes, incidencias y mantenimiento en un espacio compartido.
                        Para agencias y profesionales que cuidan de más de una web.
                    </p>
                    <div class="mt-9 flex flex-wrap items-center gap-5">
                        <Link :href="route('login')" class="btn-primary px-6">
                            Explorar la demo
                            <AppIcon name="arrow-right" :size="17" />
                        </Link>
                        <a href="#proyecto" class="btn-text">Conocer el proyecto</a>
                    </div>
                    <p class="mt-5 text-xs text-slate-500">
                        Demostración funcional con datos ficticios.
                    </p>
                </div>
                <div
                    class="relative rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-7"
                    role="img"
                    aria-label="La ficha de una web reúne datos del cliente, tickets, tareas y reportes. Diagrama de la organización del producto."
                >
                    <div class="mb-7 flex items-center justify-between text-xs text-slate-500">
                        <span>WebOps Desk</span>
                        <span class="rounded-md border border-slate-200 bg-white px-2 py-1">
                            Estructura del servicio
                        </span>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white">
                        <div class="flex items-center gap-3 border-b border-slate-200 p-5">
                            <span
                                class="grid h-11 w-11 place-items-center rounded-xl bg-indigo-50 text-indigo-700"
                            >
                                <AppIcon name="websites" :size="23" />
                            </span>
                            <div>
                                <p class="font-display font-bold">Una web. Todo su contexto.</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Ficha técnica del servicio
                                </p>
                            </div>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-6 gap-y-5 p-5">
                            <div>
                                <dt class="text-xs text-slate-500">Relación</dt>
                                <dd class="mt-1 text-sm font-medium">Cliente y contacto</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Tecnología</dt>
                                <dd class="mt-1 text-sm font-medium">Stack y mantenimiento</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Infraestructura</dt>
                                <dd class="mt-1 text-sm font-medium">Hosting y dominio</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Seguimiento</dt>
                                <dd class="mt-1 text-sm font-medium">Estado y vencimientos</dd>
                            </div>
                        </dl>
                    </div>
                    <div class="ml-5 border-l border-slate-300 pb-2 pl-6 pt-6 sm:ml-8">
                        <div
                            v-for="area in areas"
                            :key="area.icon"
                            class="relative mb-3 flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3.5 last:mb-0"
                        >
                            <span class="absolute -left-6 w-6 border-t border-slate-300" />
                            <AppIcon
                                :name="area.icon"
                                :size="17"
                                class="shrink-0 text-indigo-600"
                            />
                            <span class="text-sm font-medium">
                                {{
                                    {
                                        websites: 'Inventario de webs',
                                        tickets: 'Tickets de soporte',
                                        maintenance: 'Tareas de mantenimiento',
                                    }[area.icon]
                                }}
                            </span>
                            <AppIcon name="check" :size="15" class="ml-auto text-slate-400" />
                        </div>
                    </div>
                    <p class="mt-5 text-xs text-slate-500">
                        La información conectada. El trabajo, localizado.
                    </p>
                </div>
            </section>
            <section id="proyecto" class="border-y border-slate-200 bg-slate-50">
                <div class="mx-auto max-w-[1320px] px-5 py-16 sm:px-8 sm:py-20">
                    <div class="mb-12 grid gap-5 md:grid-cols-2 md:gap-16">
                        <h2
                            class="max-w-[22ch] text-3xl font-semibold leading-tight tracking-[-0.045em] sm:text-4xl"
                        >
                            Menos buscar información.
                            <br />
                            Más atender el servicio.
                        </h2>
                        <p class="max-w-[55ch] text-base leading-7 text-slate-600">
                            Cuando el mantenimiento se reparte entre correos, hojas de cálculo y
                            notas, se pierde el hilo. WebOps Desk conecta los datos con el trabajo
                            que depende de ellos.
                        </p>
                    </div>
                    <div class="grid gap-8 md:grid-cols-3 md:gap-10">
                        <article
                            v-for="area in areas"
                            :key="area.title"
                            class="border-t border-slate-300 pt-6"
                        >
                            <AppIcon :name="area.icon" :size="22" class="mb-5 text-indigo-600" />
                            <h3 class="text-lg font-bold tracking-tight">{{ area.title }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-600">{{ area.text }}</p>
                        </article>
                    </div>
                </div>
            </section>
            <section
                class="mx-auto grid max-w-[1320px] gap-10 px-5 py-16 sm:px-8 sm:py-20 lg:grid-cols-2 lg:gap-20"
            >
                <div>
                    <h2 class="text-2xl font-bold tracking-[-0.035em]">
                        Cada persona, su espacio.
                    </h2>
                    <p class="mt-4 max-w-[55ch] text-sm leading-7 text-slate-600">
                        El administrador organiza el servicio. Los técnicos trabajan sobre lo
                        asignado. Cada cliente consulta sus webs, solicita soporte y revisa sus
                        reportes.
                    </p>
                    <Link :href="route('login')" class="btn-text mt-5">
                        Entrar con un usuario demo
                        <AppIcon name="arrow-right" :size="16" />
                    </Link>
                </div>
                <div class="rounded-xl border border-slate-200 p-6 sm:p-7">
                    <h3 class="text-base font-bold">Una aplicación full-stack</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Laravel gestiona los datos, permisos y validaciones. Vue construye la
                        interfaz e Inertia conecta ambas partes.
                    </p>
                    <ul
                        class="mt-6 flex flex-wrap gap-x-5 gap-y-3 border-t border-slate-200 pt-5 text-xs font-medium text-slate-600"
                    >
                        <li v-for="tech in stack" :key="tech">{{ tech }}</li>
                    </ul>
                </div>
            </section>
        </main>
        <footer class="border-t border-slate-200">
            <div
                class="mx-auto flex max-w-[1320px] flex-col justify-between gap-3 px-5 py-7 text-xs text-slate-500 sm:flex-row sm:px-8"
            >
                <span>© {{ new Date().getFullYear() }} Mazmi WebOps Desk</span>
                <span>Luca Mazmishvili · Valencia, España</span>
            </div>
        </footer>
    </div>
</template>
