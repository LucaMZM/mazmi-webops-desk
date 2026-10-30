<script setup>
import { Link } from '@inertiajs/vue3';
import BrandMark from '@/Components/UI/BrandMark.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import LiquidBackdrop from '@/Components/UI/LiquidBackdrop.vue';
</script>
<template>
    <div class="webops-public-page min-h-screen">
        <LiquidBackdrop />
        <a href="#guest-content" class="skip-link">Ir al formulario</a>
        <div class="relative z-[1] mx-auto grid min-h-screen max-w-[1500px] gap-6 p-4 sm:p-6 lg:grid-cols-[minmax(0,1.12fr)_minmax(440px,.88fr)] lg:p-8">
            <aside class="glass-hero hidden min-h-[calc(100vh-64px)] flex-col justify-between p-10 lg:flex xl:p-14" data-glass-optic>
                <Link href="/" class="relative z-[1] flex items-center gap-3" aria-label="Mazmi WebOps Desk, inicio">
                    <BrandMark class="h-11 w-11 text-teal-700" />
                    <span class="font-display text-base font-bold tracking-tight">Mazmi WebOps Desk</span>
                </Link>
                <div class="relative z-[1] max-w-lg py-16">
                    <p class="webops-eyebrow mb-6">WORKSPACE / WEB OPERATIONS</p>
                    <h2 class="text-[clamp(2.8rem,4.5vw,4.9rem)] font-semibold leading-[1.03] tracking-[-0.065em] text-slate-900">Cada web merece un lugar donde todo fluya.</h2>
                    <p class="mt-7 max-w-md text-base leading-8 text-slate-600">Clientes, soporte, mantenimiento y reportes en un espacio claro, conectado y pensado para trabajar de verdad.</p>
                    <div class="mt-10 flex items-center gap-3 border-t border-white/70 pt-7 text-sm text-slate-600">
                        <AppIcon name="shield" :size="20" class="text-teal-700" />
                        Tu trabajo, con sus permisos y contexto
                    </div>
                </div>
                <p class="relative z-[1] text-xs text-slate-500">MAZMI · WEBOPS DESK</p>
            </aside>
            <main id="guest-content" class="flex min-h-[calc(100vh-32px)] flex-col rounded-[28px] border border-white/70 bg-white/35 px-6 py-7 shadow-[0_20px_64px_rgba(34,53,47,.08)] backdrop-blur-xl sm:px-12 lg:px-14">
                <Link href="/" class="flex w-fit items-center gap-2 text-sm text-slate-600 transition-colors hover:text-teal-700">
                    <AppIcon name="arrow-left" :size="16" /> Volver al inicio
                </Link>
                <div class="mx-auto flex w-full max-w-[390px] flex-1 flex-col justify-center py-14">
                    <Link href="/" class="mb-9 flex items-center gap-3 lg:hidden">
                        <BrandMark class="h-10 w-10 text-teal-700" />
                        <span class="font-display font-bold">Mazmi WebOps Desk</span>
                    </Link>
                    <slot />
                </div>
                <p class="text-center text-xs text-slate-500">Un espacio para gestionar lo importante.</p>
            </main>
        </div>
    </div>
</template>
