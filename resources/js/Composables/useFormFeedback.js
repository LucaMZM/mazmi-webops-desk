import { nextTick, ref, watch } from 'vue';

export function useFormFeedback(form) {
    const formElement = ref(null);
    watch(
        () => form.errors,
        async (errors) => {
            if (!Object.keys(errors).length) return;
            await nextTick();
            formElement.value?.querySelector('[aria-invalid="true"]')?.focus();
        },
        { deep: true },
    );
    return { formElement };
}
