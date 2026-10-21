<script setup>
defineProps({ processing: Boolean });
</script>
<template>
    <button type="submit" class="btn-primary" :disabled="processing" :aria-busy="processing">
        <svg
            v-if="processing"
            class="h-4 w-4 animate-spin motion-reduce:animate-none"
            viewBox="0 0 20 20"
            fill="none"
            aria-hidden="true"
        >
            <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="2" opacity=".3" />
            <path
                d="M10 3a7 7 0 0 1 7 7"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
            />
        </svg>
        <slot />
    </button>
</template>
