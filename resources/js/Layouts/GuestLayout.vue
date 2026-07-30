<script setup>
import { Link } from '@inertiajs/vue3';
</script>
<template>
    <div class="flex min-h-screen flex-col items-center justify-center bg-slate-950 px-4 py-10">
        <Link href="/" class="mb-7 flex items-center gap-3 text-white">
            <div
                class="grid h-11 w-11 place-items-center rounded-xl bg-indigo-500 text-lg font-black"
            >
                M
            </div>
            <div>
                <p class="font-bold">Mazmi WebOps Desk</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-400">
                    Área de operaciones web
                </p>
            </div>
        </Link>
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white p-6 shadow-2xl sm:p-8">
            <slot />
        </div>
    </div>
</template>
