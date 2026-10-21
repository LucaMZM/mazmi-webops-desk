<script setup>
defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message" role="alert">
        <p class="text-xs leading-5 text-red-700">
            {{ message }}
        </p>
    </div>
</template>
