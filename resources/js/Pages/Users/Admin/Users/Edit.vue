<script setup>
import { Head, usePage, useForm } from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import ImageUpload from "@/Components/Form/ImageUpload.vue";

const props = defineProps({
    user: Object,
    profile: Object,
});

const form = useForm({
    first_name: props.profile.first_name,
    middle_name: props.profile.middle_name ?? "",
    last_name: props.profile.last_name,
});

// const hasProfileSetup = usePage().props.auth.hasProfileSetup;
// const isServiceProvider = usePage().props.auth.isServiceProvider;
</script>

<template>
    <Head title="Edit" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('admin.users.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ user.name }} - Edit
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <form
                        @submit.prevent="
                            form.put(route('admin.users.update', user.id))
                        "
                        class="mt-6 space-y-6"
                    >
                        <div>
                            <InputLabel for="first_name" value="First Name" />

                            <TextInput
                                id="first_name"
                                type="text"
                                class="block w-full mt-1"
                                v-model="form.first_name"
                                required
                                autocomplete="first_name"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.first_name"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="middle_name"
                                value="Middle Name"
                                isOptional
                            />

                            <TextInput
                                id="middle_name"
                                type="text"
                                class="block w-full mt-1"
                                v-model="form.middle_name"
                                autocomplete="middle_name"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.middle_name"
                            />
                        </div>

                        <div>
                            <InputLabel for="last_name" value="Last Name" />

                            <TextInput
                                id="last_name"
                                type="text"
                                class="block w-full mt-1"
                                v-model="form.last_name"
                                required
                                autocomplete="last_name"
                            />

                            <InputError
                                class="mt-2"
                                :message="form.errors.last_name"
                            />
                        </div>

                        <div>
                            <InputLabel for="address" value="Address" />

                            <TextInput
                                id="address"
                                type="text"
                                class="block w-full mt-1"
                                :value="
                                    profile.address
                                        ? profile.address
                                        : 'No address given'
                                "
                                readonly
                            />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing"
                                >Save</PrimaryButton
                            >

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-if="form.recentlySuccessful"
                                    class="text-sm text-gray-600"
                                >
                                    Saved.
                                </p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
