<script setup>
import { onUnmounted, ref, useId } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    url: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        default: '¿Eliminar este registro?',
    },
    message: {
        type: String,
        default: 'Esta acción no se puede deshacer.',
    },
});

const open = ref(false);
const processing = ref(false);
const trigger = ref();
const titleId = useId();
const descriptionId = useId();
let focusTimer;

const restoreFocus = () => {
    clearTimeout(focusTimer);
    focusTimer = setTimeout(() => trigger.value?.focus(), 220);
};

const openModal = () => {
    if (!processing.value) {
        open.value = true;
    }
};

const closeModal = () => {
    if (!processing.value) {
        open.value = false;
        restoreFocus();
    }
};

const remove = () => {
    if (processing.value) {
        return;
    }

    processing.value = true;

    router.delete(props.url, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
        },
        onFinish: () => {
            processing.value = false;

            if (!open.value) {
                restoreFocus();
            }
        },
    });
};

onUnmounted(() => clearTimeout(focusTimer));
</script>

<template>
    <button ref="trigger" class="btn-danger" type="button" @click="openModal">
        <slot>Eliminar</slot>
    </button>

    <Modal
        :show="open"
        max-width="md"
        :closeable="!processing"
        panel-class="rounded-2xl"
        overlay-class="bg-slate-950/50"
        :aria-labelledby="titleId"
        :aria-describedby="descriptionId"
        @close="closeModal"
    >
        <div class="p-6" :aria-busy="processing">
            <h3 :id="titleId" class="text-lg font-bold text-slate-950">
                {{ title }}
            </h3>
            <p :id="descriptionId" class="mt-2 text-sm text-slate-500">
                {{ message }}
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button
                    class="btn-secondary"
                    type="button"
                    autofocus
                    :disabled="processing"
                    @click="closeModal"
                >
                    Cancelar
                </button>
                <button class="btn-danger" type="button" :disabled="processing" @click="remove">
                    {{ processing ? 'Eliminando…' : 'Sí, eliminar' }}
                </button>
            </div>
        </div>
    </Modal>
</template>
