<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const visible = ref(true);
const message = computed(() => page.props.flash?.success || page.props.flash?.error);
const type = computed(() => (page.props.flash?.error ? 'error' : 'success'));
watch(message, () => (visible.value = true));
</script>
<template>
    <div
        v-if="message && visible"
        :class="[
            'mb-6 flex items-center justify-between rounded-xl border px-4 py-3 text-sm font-medium',
            type === 'error'
                ? 'border-red-200 bg-red-50 text-red-800'
                : 'border-emerald-200 bg-emerald-50 text-emerald-800',
        ]"
    >
        <span>{{ message }}</span>
        <button
            type="button"
            class="grid h-9 w-9 place-items-center rounded-lg text-lg hover:bg-black/5"
            aria-label="Cerrar mensaje"
            @click="visible = false"
        >
            ×
        </button>
    </div>
</template>
