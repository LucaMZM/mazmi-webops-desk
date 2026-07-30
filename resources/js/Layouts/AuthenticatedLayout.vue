<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/UI/FlashMessage.vue';

defineProps({ title: { type: String, default: '' } });
const page = usePage();
const mobileOpen = ref(false);
const user = computed(() => page.props.auth.user);
const roleLabel = { admin: 'Administrador', technician: 'Técnico', client: 'Cliente' };
const nav = [
    { label: 'Dashboard', route: 'dashboard', match: 'dashboard', icon: '▦' },
    { label: 'Clientes', route: 'clients.index', match: 'clients.*', icon: '◉' },
    { label: 'Webs', route: 'websites.index', match: 'websites.*', icon: '◇' },
    { label: 'Tickets', route: 'tickets.index', match: 'tickets.*', icon: '◫' },
    { label: 'Mantenimiento', route: 'maintenance.index', match: 'maintenance.*', icon: '✓' },
    { label: 'Reportes', route: 'reports.index', match: 'reports.*', icon: '≡' },
];
</script>

<template>
    <div class="min-h-screen overflow-x-hidden bg-slate-50">
        <div
            v-if="mobileOpen"
            class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden"
            @click="mobileOpen = false"
        />
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-950 text-white transition-transform lg:translate-x-0',
                mobileOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <div class="flex h-20 items-center gap-3 border-b border-white/10 px-6">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-indigo-500 font-black">
                    M
                </div>
                <div>
                    <p class="font-bold">Mazmi WebOps Desk</p>
                    <p class="text-xs text-slate-400">Área de operaciones web</p>
                </div>
            </div>
            <nav class="flex-1 space-y-1 p-4">
                <Link
                    v-for="item in nav"
                    :key="item.route"
                    :href="route(item.route)"
                    :class="[
                        'flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition',
                        route().current(item.match)
                            ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-950/30'
                            : 'text-slate-300 hover:bg-white/10 hover:text-white',
                    ]"
                    @click="mobileOpen = false"
                >
                    <span class="grid h-7 w-7 place-items-center rounded-lg bg-white/10 text-base">
                        {{ item.icon }}
                    </span>
                    {{ item.label }}
                </Link>
            </nav>
            <div class="border-t border-white/10 p-4">
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 rounded-xl p-3 hover:bg-white/10"
                >
                    <div
                        class="grid h-10 w-10 place-items-center rounded-full bg-indigo-200 font-bold text-indigo-900"
                    >
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold">{{ user.name }}</p>
                        <p class="text-xs text-slate-400">{{ roleLabel[user.role] }}</p>
                    </div>
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="mt-2 w-full rounded-lg px-3 py-2 text-left text-xs font-medium text-slate-400 hover:bg-white/10 hover:text-white"
                >
                    Cerrar sesión
                </Link>
            </div>
        </aside>

        <div class="lg:pl-72">
            <header
                class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-8 lg:h-20"
            >
                <div class="flex items-center gap-3">
                    <button
                        class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 lg:hidden"
                        @click="mobileOpen = true"
                        aria-label="Abrir menú"
                    >
                        ☰
                    </button>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-600">
                            Mazmi WebOps Desk
                        </p>
                        <h1 class="text-lg font-bold text-slate-900 sm:text-xl">{{ title }}</h1>
                    </div>
                </div>
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold">{{ user.name }}</p>
                    <p class="text-xs text-slate-500">{{ roleLabel[user.role] }}</p>
                </div>
            </header>
            <main class="p-4 pb-24 sm:p-8 lg:pb-8">
                <FlashMessage />
                <slot />
            </main>
        </div>

        <nav
            class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-5 border-t border-slate-200 bg-white px-1 py-2 lg:hidden"
        >
            <Link
                v-for="item in nav.slice(0, 5)"
                :key="item.route"
                :href="route(item.route)"
                :class="[
                    'flex flex-col items-center gap-1 rounded-lg py-1 text-[10px] font-semibold',
                    route().current(item.match) ? 'text-indigo-600' : 'text-slate-500',
                ]"
            >
                <span class="text-lg">{{ item.icon }}</span>
                {{ item.label === 'Mantenimiento' ? 'Tareas' : item.label }}
            </Link>
        </nav>
    </div>
</template>
