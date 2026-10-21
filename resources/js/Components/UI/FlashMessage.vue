<script setup>
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppIcon from '@/Components/UI/AppIcon.vue';
const page = usePage();
const visible = ref(true);
const message = computed(() => page.props.flash?.success || page.props.flash?.error);
const type = computed(() => (page.props.flash?.error ? 'error' : 'success'));
watch(message, () => (visible.value = true));
</script>
<template>
    <div aria-live="polite" aria-atomic="true">
        <div
            v-if="message && visible"
            :class="[
                'mb-6 flex items-center gap-3 rounded-lg border px-4 py-3 text-sm',
                type === 'error'
                    ? 'border-red-200 bg-red-50 text-red-800'
                    : 'border-emerald-200 bg-emerald-50 text-emerald-800',
            ]"
        >
            <AppIcon :name="type === 'error' ? 'alert' : 'check'" :size="18" class="shrink-0" />
            <span class="flex-1">{{ message }}</span>
            <button
                type="button"
                class="grid h-9 w-9 shrink-0 place-items-center rounded-md hover:bg-black/5"
                aria-label="Cerrar mensaje"
                @click="visible = false"
            >
                <AppIcon name="close" :size="16" />
            </button>
        </div>
    </div>
</template>
