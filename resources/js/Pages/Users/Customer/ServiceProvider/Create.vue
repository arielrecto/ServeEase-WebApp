<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import ImageUpload from "@/Components/Form/ImageUpload.vue";

defineProps({
    serviceTypes: Object,
    service: Object,
});

const form = useForm({
    service: null,
    experience: null,
    contact: null,
    certificate: null,
});

const submit = () => {
    form.post(route("customer.service-provider.store"), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('customer.dashboard')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Apply as Service Provider
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div class="relative p-6">
                        <p class="text-gray-600">
                            Fill out the form for your application for service
                            provider in <strong>ServEase</strong>. Make sure
                            that the information you provide are correct and
                            legitimate.
                        </p>

                        <section class="max-w-xl">
                            <form
                                @submit.prevent="submit"
                                class="mt-6 space-y-6"
                            >
                                <div>
                                    <InputLabel
                                        for="service"
                                        value="Select a service that you offer"
                                    />

                                    <SelectInput
                                        id="service"
                                        :choices="serviceTypes"
                                        optionKey="name"
                                        valueKey="id"
                                        class="block w-full mt-1"
                                        v-model="form.service"
                                        required
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.service"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="experience"
                                        value="How many years of experience have you been offering your service?"
                                    />

                                    <TextInput
                                        id="experience"
                                        type="text"
                                        class="block w-full mt-1"
                                        v-model="form.experience"
                                        required
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.experience"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="contact"
                                        value="Provide a contact number. This is visible to the customers."
                                    />

                                    <TextInput
                                        id="contact"
                                        type="text"
                                        class="block w-full mt-1"
                                        v-model="form.contact"
                                        required
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.contact"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="certificate"
                                        value="Upload an image to prove your legitimacy as a provider of your service."
                                    />

                                    <ImageUpload
                                        @success="
                                            (src) => (form.certificate = src)
                                        "
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.certificate"
                                    />

                                    <ul class="mt-2 text-gray-600">
                                        The proof image can be any of the
                                        following:
                                        <li class="list-disc list-inside">
                                            Certificates
                                        </li>
                                        <li class="list-disc list-inside">
                                            Valid IDs
                                        </li>
                                    </ul>
                                </div>

                                <PrimaryButton :disabled="form.processing"
                                    >Submit</PrimaryButton
                                >
                            </form>
                        </section>

                        <div
                            v-if="!service?.verified_at"
                            class="absolute top-0 left-0 flex items-center justify-center w-full h-full backdrop-blur-sm"
                        >
                            <div class="p-4 bg-white rounded-md">
                                Application sent! Please wait for the approval.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
