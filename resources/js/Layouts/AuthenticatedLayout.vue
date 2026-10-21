<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import FlashMessage from '@/Components/UI/FlashMessage.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import BrandMark from '@/Components/UI/BrandMark.vue';

defineProps({ title: { type: String, default: '' } });
const page = usePage();
const user = computed(() => page.props.auth.user);
const roleLabel = { admin: 'Administrador', technician: 'Técnico', client: 'Cliente' };
const mobileOpen = ref(false);
const drawer = ref(null);
const menuTrigger = ref(null);
const nav = [
    { label: 'Resumen', route: 'dashboard', match: 'dashboard', icon: 'dashboard' },
    { label: 'Webs', route: 'websites.index', match: 'websites.*', icon: 'websites' },
    { label: 'Tickets', route: 'tickets.index', match: 'tickets.*', icon: 'tickets' },
    {
        label: 'Mantenimiento',
        route: 'maintenance.index',
        match: 'maintenance.*',
        icon: 'maintenance',
    },
    { label: 'Clientes', route: 'clients.index', match: 'clients.*', icon: 'clients' },
    { label: 'Reportes', route: 'reports.index', match: 'reports.*', icon: 'reports' },
];
const initials = computed(() =>
    user.value.name
        .split(' ')
        .slice(0, 2)
        .map((part) => part[0])
        .join(''),
);
const date = new Intl.DateTimeFormat('es-ES', { day: 'numeric', month: 'long' }).format(new Date());
watch(mobileOpen, async (open) => {
    if (open) {
        await nextTick();
        drawer.value?.showModal();
        document.body.style.overflow = 'hidden';
    } else {
        drawer.value?.close();
        document.body.style.overflow = '';
        menuTrigger.value?.focus();
    }
});
const closeAtDesktop = () => {
    if (window.innerWidth >= 1024) mobileOpen.value = false;
};
onMounted(() => window.addEventListener('resize', closeAtDesktop));
onUnmounted(() => {
    document.body.style.overflow = '';
    window.removeEventListener('resize', closeAtDesktop);
});
</script>
<template>
    <div class="app-shell min-h-screen bg-slate-50">
        <a href="#main-content" class="skip-link">Ir al contenido</a>
        <aside
            class="app-navigation fixed inset-y-0 left-0 z-40 hidden w-[232px] flex-col border-r border-slate-200 bg-white lg:flex"
        >
            <Link
                :href="route('dashboard')"
                class="flex h-24 items-center gap-3 px-6"
                aria-label="Mazmi WebOps Desk, resumen"
            >
                <BrandMark class="h-10 w-10 shrink-0 text-indigo-600" />
                <div translate="no">
                    <p
                        class="font-display text-lg font-extrabold tracking-[-0.04em] text-slate-900"
                    >
                        Mazmi
                    </p>
                    <p class="-mt-0.5 text-xs text-slate-500">WebOps Desk</p>
                </div>
            </Link>
            <div class="mx-4 mb-6 rounded-lg border border-slate-200 bg-slate-50 px-3 py-3">
                <div class="flex items-center gap-2.5">
                    <AppIcon name="building" :size="17" class="text-slate-500" />
                    <span class="text-sm font-medium">Operaciones web</span>
                </div>
                <p class="ml-[27px] mt-1 text-xs text-slate-500">Espacio de demostración</p>
            </div>
            <nav class="flex-1 space-y-1 px-3" aria-label="Navegación principal">
                <Link
                    v-for="item in nav"
                    :key="item.route"
                    :href="route(item.route)"
                    :aria-current="route().current(item.match) ? 'page' : undefined"
                    :class="[
                        'nav-item flex min-h-11 items-center gap-3 rounded-lg px-3.5 py-2.5 text-sm transition-colors',
                        route().current(item.match)
                            ? 'bg-indigo-50 font-semibold text-indigo-700'
                            : 'font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    ]"
                >
                    <AppIcon :name="item.icon" :size="18" />
                    {{ item.label }}
                    <span
                        v-if="route().current(item.match)"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-indigo-600"
                    />
                </Link>
            </nav>
            <div class="m-4 border-t border-slate-200 pt-4">
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 rounded-lg p-2 hover:bg-slate-50"
                >
                    <span
                        class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-slate-900 text-xs font-semibold text-white"
                    >
                        {{ initials }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium">{{ user.name }}</p>
                        <p class="text-xs text-slate-500">{{ roleLabel[user.role] }}</p>
                    </div>
                    <AppIcon name="settings" :size="16" class="text-slate-500" />
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="mt-2 flex min-h-10 w-full items-center gap-2.5 rounded-lg px-2 text-xs text-slate-500 hover:bg-slate-50 hover:text-slate-900"
                >
                    <AppIcon name="logout" :size="16" />
                    Cerrar sesión
                </Link>
            </div>
        </aside>
        <dialog
            ref="drawer"
            class="app-navigation m-0 h-dvh max-h-none w-[min(320px,88vw)] max-w-none overscroll-contain border-0 bg-white p-0 backdrop:bg-slate-950/40 lg:hidden"
            aria-label="Menú principal"
            @cancel.prevent="mobileOpen = false"
            @click="
                (event) => {
                    if (event.target === drawer) mobileOpen = false;
                }
            "
        >
            <div class="flex h-full flex-col p-5">
                <div class="mb-8 flex items-center gap-3">
                    <BrandMark class="h-9 w-9 text-indigo-600" />
                    <span class="font-display font-bold">Mazmi WebOps Desk</span>
                    <button
                        type="button"
                        class="ml-auto grid h-11 w-11 place-items-center rounded-lg hover:bg-slate-50"
                        aria-label="Cerrar menú"
                        @click="mobileOpen = false"
                    >
                        <AppIcon name="close" :size="20" />
                    </button>
                </div>
                <nav class="flex-1 space-y-2" aria-label="Navegación móvil">
                    <Link
                        v-for="item in nav"
                        :key="item.route"
                        :href="route(item.route)"
                        :aria-current="route().current(item.match) ? 'page' : undefined"
                        :class="[
                            'flex min-h-12 items-center gap-3 rounded-lg px-4 text-base',
                            route().current(item.match)
                                ? 'bg-indigo-50 font-semibold text-indigo-700'
                                : 'text-slate-600 hover:bg-slate-50',
                        ]"
                        @click="mobileOpen = false"
                    >
                        <AppIcon :name="item.icon" :size="20" />
                        {{ item.label }}
                    </Link>
                </nav>
                <Link
                    :href="route('profile.edit')"
                    class="border-t border-slate-200 px-4 py-4 font-medium"
                    @click="mobileOpen = false"
                >
                    {{ user.name }}
                    <span class="mt-1 block text-xs text-slate-500">
                        {{ roleLabel[user.role] }} · Mi cuenta
                    </span>
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="btn-secondary w-full"
                    @click="mobileOpen = false"
                >
                    <AppIcon name="logout" :size="16" />
                    Cerrar sesión
                </Link>
            </div>
        </dialog>
        <div class="app-content lg:pl-[232px]">
            <header
                class="app-header flex h-[72px] items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 sm:px-7 lg:px-9"
            >
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        ref="menuTrigger"
                        type="button"
                        class="grid h-11 w-11 shrink-0 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 lg:hidden"
                        aria-label="Abrir menú"
                        :aria-expanded="mobileOpen"
                        @click="mobileOpen = true"
                    >
                        <AppIcon name="menu" :size="20" />
                    </button>
                    <span class="hidden text-sm text-slate-500 sm:block">Espacio de trabajo</span>
                    <AppIcon
                        name="chevron-right"
                        :size="14"
                        class="hidden text-slate-400 sm:block"
                    />
                    <span class="truncate text-sm font-medium text-slate-800">
                        {{ title === 'Dashboard' ? 'Resumen' : title }}
                    </span>
                </div>
                <div class="flex shrink-0 items-center gap-4">
                    <time class="hidden text-xs text-slate-500 md:block">{{ date }}</time>
                    <span class="hidden h-5 border-l border-slate-200 md:block" />
                    <Link
                        :href="route('profile.edit')"
                        class="flex items-center gap-2 rounded-lg py-1.5 pl-1.5 pr-2 hover:bg-slate-50"
                        title="Mi cuenta"
                    >
                        <span
                            class="grid h-8 w-8 place-items-center rounded-full bg-slate-100 text-xs font-semibold text-slate-700"
                        >
                            {{ initials }}
                        </span>
                        <span class="hidden text-xs font-medium text-slate-600 sm:block">
                            {{ roleLabel[user.role] }}
                        </span>
                    </Link>
                </div>
            </header>
            <main
                id="main-content"
                tabindex="-1"
                class="px-4 py-6 outline-none sm:px-7 sm:py-8 lg:px-9 lg:py-9"
            >
                <div class="mx-auto w-full max-w-[1500px]">
                    <FlashMessage />
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
