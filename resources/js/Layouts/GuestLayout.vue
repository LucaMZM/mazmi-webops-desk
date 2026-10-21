<script setup>
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/UI/BrandMark.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
</script>
<template>
    <div class="min-h-screen bg-white lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(480px,1fr)]">
        <a href="#guest-content" class="skip-link">Ir al formulario</a>
        <aside
            class="relative hidden min-h-screen flex-col justify-between overflow-hidden bg-slate-900 p-12 text-white lg:flex xl:p-16"
        >
            <Link href="/" class="flex items-center gap-3" aria-label="Mazmi WebOps Desk, inicio">
                <BrandMark class="h-11 w-11 text-white/15" />
                <span class="font-display text-lg font-bold tracking-tight" translate="no">
                    Mazmi WebOps Desk
                </span>
            </Link>
            <div class="relative max-w-lg py-16">
                <p class="mb-5 text-sm text-slate-300">Para quienes cuidan de la web.</p>
                <h2
                    class="text-[clamp(2.5rem,4vw,4.5rem)] font-semibold leading-[1.12] tracking-[-0.055em]"
                >
                    Cada web tiene
                    <br />
                    su trabajo detrás.
                </h2>
                <p class="mt-7 max-w-sm text-base leading-7 text-slate-300">
                    Clientes, incidencias y mantenimiento. El contexto que necesitas para llevar el
                    servicio al día.
                </p>
                <div
                    class="mt-10 flex items-center gap-3 border-t border-white/15 pt-6 text-sm text-slate-300"
                >
                    <AppIcon name="shield" :size="20" />
                    Un espacio para cada rol
                </div>
            </div>
            <p class="text-xs text-slate-300">
                Mazmi WebOps Desk
                <span class="mx-2 text-slate-400">/</span>
                Entorno de demostración
            </p>
        </aside>
        <main id="guest-content" class="flex min-h-screen flex-col px-6 py-8 sm:px-12 lg:px-14">
            <Link
                href="/"
                class="flex w-fit items-center gap-2 text-sm text-slate-600 hover:text-indigo-700"
            >
                <AppIcon name="arrow-left" :size="16" />
                Volver al inicio
            </Link>
            <div class="mx-auto flex w-full max-w-[390px] flex-1 flex-col justify-center py-12">
                <Link href="/" class="mb-9 flex items-center gap-3 lg:hidden">
                    <BrandMark class="h-10 w-10 text-indigo-600" />
                    <span class="font-display font-bold">Mazmi WebOps Desk</span>
                </Link>
                <slot />
            </div>
            <p class="text-center text-xs text-slate-500">Mantenimiento web, con contexto.</p>
        </main>
    </div>
</template>
