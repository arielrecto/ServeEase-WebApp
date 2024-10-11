<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateAccountInformationForm from './Partials/UpdateAccountInformationForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateProviderProfileForm from './Partials/UpdateProviderProfileForm.vue';
import { Head, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    sexChoices: Object,
    profile: Object,
    providerProfile: Object,
    serviceTypes: Array,
});

const hasProfileSetup = usePage().props.auth.hasProfileSetup;
const isServiceProvider = usePage().props.auth.isServiceProvider;
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <UpdateProfileInformationForm class="max-w-xl" :profile="profile" :sexChoices="sexChoices" />
                </div>

                <template v-if="hasProfileSetup">
                    <div v-if="isServiceProvider" class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                        <!-- <UpdateProviderProfileForm :providerProfile="providerProfile" :serviceTypes="serviceTypes" class="max-w-xl" /> -->
                    </div>

                    <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                        <UpdateAccountInformationForm :must-verify-email="mustVerifyEmail" :status="status"
                            class="max-w-xl" />
                    </div>

                    <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>

                    <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
