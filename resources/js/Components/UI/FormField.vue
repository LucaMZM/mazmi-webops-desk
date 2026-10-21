<script setup>
import { onMounted, onUpdated, ref, useId } from 'vue';
const props = defineProps({ label: String, error: String, hint: String, required: Boolean });
const field = ref(null);
const id = useId();
const connectControl = () => {
    const control = field.value?.querySelector('input, select, textarea');
    if (!control) return;
    control.id ||= id;
    control.name ||= props.label;
    if (props.error || props.hint) control.setAttribute('aria-describedby', id + '-message');
    else control.removeAttribute('aria-describedby');
    control.setAttribute('aria-invalid', props.error ? 'true' : 'false');
    if (props.required) control.setAttribute('aria-required', 'true');
};
onMounted(connectControl);
onUpdated(connectControl);
</script>
<template>
    <label ref="field" class="block min-w-0">
        <span class="mb-2 flex items-center gap-1 text-sm font-medium text-slate-700">
            {{ label }}
            <span v-if="required" aria-hidden="true" class="text-slate-500">*</span>
        </span>
        <slot />
        <span
            v-if="error"
            :id="id + '-message'"
            role="alert"
            class="mt-2 block text-xs leading-5 text-red-700"
        >
            {{ error }}
        </span>
        <span
            v-else-if="hint"
            :id="id + '-message'"
            class="mt-2 block text-xs leading-5 text-slate-500"
        >
            {{ hint }}
        </span>
    </label>
</template>
