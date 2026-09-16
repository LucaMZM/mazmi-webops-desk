<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/UI/FlashMessage.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';

defineProps({ title: { type: String, default: '' } });
const page = usePage();
const mobileOpen = ref(false);
const user = computed(() => page.props.auth.user);
const roleLabel = { admin: 'Administrador', technician: 'Técnico', client: 'Cliente' };
const nav = [
    { label: 'Dashboard', route: 'dashboard', match: 'dashboard', icon: 'dashboard' },
    { label: 'Clientes', route: 'clients.index', match: 'clients.*', icon: 'clients' },
    { label: 'Webs', route: 'websites.index', match: 'websites.*', icon: 'websites' },
    { label: 'Tickets', route: 'tickets.index', match: 'tickets.*', icon: 'tickets' },
    {
        label: 'Mantenimiento',
        route: 'maintenance.index',
        match: 'maintenance.*',
        icon: 'maintenance',
    },
    { label: 'Reportes', route: 'reports.index', match: 'reports.*', icon: 'reports' },
];
const closeOnEscape = (event) => {
    if (event.key === 'Escape') {
        mobileOpen.value = false;
    }
};

watch(mobileOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

onMounted(() => window.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    window.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = '';
});
</script>

<template>
    <div class="min-h-screen overflow-x-hidden bg-[#f6f8fb]">
        <div
            v-if="mobileOpen"
            class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-[2px] lg:hidden"
            @click="mobileOpen = false"
        />
        <aside
            :class="[
                'app-navigation fixed inset-y-0 left-0 z-50 flex w-60 flex-col border-r border-white/[0.06] bg-[#07111f] text-white shadow-2xl shadow-slate-950/20 transition-transform duration-200 lg:translate-x-0 lg:shadow-none',
                mobileOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <div class="flex h-16 items-center gap-3 border-b border-white/[0.07] px-5">
                <div
                    class="grid h-9 w-9 place-items-center rounded-md bg-indigo-500 text-sm font-extrabold"
                >
                    M
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-bold tracking-tight">Mazmi WebOps Desk</p>
                    <p class="text-[11px] text-slate-400">Operaciones web</p>
                </div>
                <button
                    type="button"
                    class="grid h-9 w-9 place-items-center rounded-md text-slate-400 hover:bg-white/10 hover:text-white lg:hidden"
                    aria-label="Cerrar menú"
                    @click="mobileOpen = false"
                >
                    <AppIcon name="close" :size="18" />
                </button>
            </div>
            <nav class="flex-1 space-y-1.5 px-3 py-5" aria-label="Navegación principal">
                <p
                    class="mb-3 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500"
                >
                    Espacio de trabajo
                </p>
                <Link
                    v-for="item in nav"
                    :key="item.route"
                    :href="route(item.route)"
                    :class="[
                        'group flex min-h-10 items-center gap-3 rounded-md border px-3 py-2 text-sm font-medium transition',
                        route().current(item.match)
                            ? 'border-indigo-500 bg-indigo-600 text-white'
                            : 'border-transparent text-slate-400 hover:bg-white/[0.05] hover:text-slate-100',
                    ]"
                    @click="mobileOpen = false"
                >
                    <span
                        :class="[
                            'grid h-7 w-7 place-items-center transition',
                            route().current(item.match)
                                ? 'text-white'
                                : 'text-slate-400 group-hover:text-slate-200',
                        ]"
                    >
                        <AppIcon :name="item.icon" :size="17" />
                    </span>
                    {{ item.label }}
                </Link>
            </nav>
            <div class="border-t border-white/[0.07] p-3">
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 rounded-md p-3 transition hover:bg-white/[0.06]"
                >
                    <div
                        class="grid h-9 w-9 place-items-center rounded-md bg-indigo-100 text-sm font-bold text-indigo-800"
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
                    class="mt-1 flex min-h-10 w-full items-center gap-2 rounded-md px-3 text-left text-xs font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white"
                >
                    <AppIcon name="logout" :size="16" />
                    Cerrar sesión
                </Link>
            </div>
        </aside>

        <div class="app-content lg:pl-60">
            <header
                class="app-header sticky top-0 z-30 flex h-14 items-center justify-between border-b border-slate-200 bg-white/90 px-4 backdrop-blur-xl sm:px-6 lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="grid h-10 w-10 place-items-center rounded-md border border-slate-200 bg-white text-slate-700 lg:hidden"
                        @click="mobileOpen = true"
                        aria-label="Abrir menú"
                    >
                        <AppIcon name="menu" :size="19" />
                    </button>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-semibold text-slate-900">WebOps Desk</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-500">{{ title }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-slate-900">{{ user.name }}</p>
                        <p class="text-xs text-slate-500">{{ roleLabel[user.role] }}</p>
                    </div>
                    <div
                        class="grid h-9 w-9 place-items-center rounded-md border border-indigo-100 bg-indigo-50 text-sm font-bold text-indigo-700"
                    >
                        {{ user.name.charAt(0) }}
                    </div>
                </div>
            </header>
            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto w-full max-w-[1480px]">
                    <FlashMessage />
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
