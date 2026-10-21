<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
    panelClass: {
        type: String,
        default: 'rounded-xl',
    },
    overlayClass: {
        type: String,
        default: 'bg-slate-950/35',
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);
let closeTimer;

watch(
    () => props.show,
    async (show) => {
        if (show) {
            clearTimeout(closeTimer);
            document.body.style.overflow = 'hidden';
            showSlot.value = true;
            await nextTick();

            if (dialog.value && !dialog.value.open) {
                dialog.value.showModal();
            }
        } else {
            document.body.style.overflow = '';

            closeTimer = setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

onMounted(() => {
    if (props.show && dialog.value && !dialog.value.open) {
        document.body.style.overflow = 'hidden';
        dialog.value.showModal();
    }
});

onUnmounted(() => {
    clearTimeout(closeTimer);
    document.body.style.overflow = '';
});

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <dialog
        ref="dialog"
        class="z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-transparent"
        @cancel.prevent="close"
        @keydown.esc.prevent.stop="close"
    >
        <div
            class="fixed inset-0 z-50 grid items-center overflow-y-auto overscroll-contain px-4 py-6"
            scroll-region
        >
            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <button
                    v-show="show"
                    type="button"
                    aria-label="Cerrar diálogo"
                    class="fixed inset-0 w-full transition-opacity"
                    @click="close"
                >
                    <span class="absolute inset-0" :class="overlayClass" />
                </button>
            </Transition>

            <Transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-show="show"
                    class="relative mx-auto w-full transform overflow-hidden border border-slate-200 bg-white shadow-xl transition-[opacity,transform]"
                    :class="[maxWidthClass, panelClass]"
                >
                    <slot v-if="showSlot" />
                </div>
            </Transition>
        </div>
    </dialog>
</template>
