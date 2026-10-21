<script setup>
import PageHeader from '@/Components/UI/PageHeader.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout title="Perfil">
        <PageHeader
            title="Mi cuenta"
            description="Datos de acceso, contraseña y preferencias de tu perfil."
        />
        <div>
            <div class="max-w-4xl space-y-6">
                <div class="panel p-5 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="panel p-5 sm:p-8">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="panel p-5 sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
