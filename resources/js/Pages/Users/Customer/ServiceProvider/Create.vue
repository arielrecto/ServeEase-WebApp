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
    experience_duration: null,
    contact: null,
    valid_id_type: null,
    valid_id_image: null,
    citizenship_document_type: null,
    citizenship_document_image: null,
    proof_document_image: null,
});

const experienceDurationOptions = [
    { name: "Years", value: "years" },
    { name: "Months", value: "months" },
];

const validIdTypes = [
    { name: "Driver's License" },
    { name: "Passport" },
    { name: "PhilSys ID (National ID)" },
    { name: "PhilHealth ID (PhilHealth Card)" },
    { name: "Unified Multi-Purpose ID (UMID)" },
];

const citizenshipDocumentTypes = [
    { name: "Barangay ID" },
    { name: "Certificate of Barangay Indigency" },
];

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
                                <h2 class="font-bold">Provider Information</h2>
                                <div>
                                    <InputLabel
                                        for="service"
                                        value="Select the service that you offer"
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

                                <div class="flex items-end gap-4">
                                    <div>
                                        <InputLabel
                                            for="experience"
                                            value="How many years of experience have you been offering your service?"
                                        />

                                        <div>
                                            <TextInput
                                                id="experience"
                                                type="number"
                                                class="block w-full mt-1"
                                                v-model="form.experience"
                                                required
                                            />

                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors.experience
                                                "
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <!-- <InputLabel
                                            for="experienceDuration"
                                            value="Select the valid ID that you will submit."
                                        /> -->

                                        <SelectInput
                                            id="experienceDuration"
                                            :choices="experienceDurationOptions"
                                            optionKey="name"
                                            valueKey="name"
                                            class="block w-full mt-1"
                                            v-model="form.experience_duration"
                                            required
                                        />

                                        <InputError
                                            class="mt-2"
                                            :message="
                                                form.errors.experience_duration
                                            "
                                        />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel
                                        for="contact"
                                        value="Provide a contact number. This will be visible to the customers."
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

                                <h2 class="!mt-10 font-bold">
                                    Submit Documents
                                </h2>

                                <div>
                                    <InputLabel
                                        for="service"
                                        value="Select the valid ID that you will submit."
                                    />

                                    <SelectInput
                                        id="service"
                                        :choices="validIdTypes"
                                        optionKey="name"
                                        valueKey="name"
                                        class="block w-full mt-1"
                                        v-model="form.valid_id_type"
                                        required
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.validIdType"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="valid_id_image"
                                        value="Upload an image of your chosen valid ID."
                                    />

                                    <ImageUpload
                                        @success="
                                            (src) => (form.valid_id_image = src)
                                        "
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="form.errors.valid_id_image"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="service"
                                        value="Select the type of document you will submit to prove your Bacoor citizenship."
                                    />

                                    <SelectInput
                                        id="service"
                                        :choices="citizenshipDocumentTypes"
                                        optionKey="name"
                                        valueKey="name"
                                        class="block w-full mt-1"
                                        v-model="form.citizenship_document_type"
                                        required
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors
                                                .citizenship_document_type
                                        "
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="valid_id_image"
                                        value="Upload an image of your chosen citizenship document."
                                    />

                                    <ImageUpload
                                        @success="
                                            (src) =>
                                                (form.citizenship_document_image =
                                                    src)
                                        "
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors
                                                .citizenship_document_image
                                        "
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="proof_document_image"
                                        value="Upload an image to prove your legitimacy as a provider of your service."
                                    />

                                    <ImageUpload
                                        @success="
                                            (src) =>
                                                (form.proof_document_image =
                                                    src)
                                        "
                                    />

                                    <InputError
                                        class="mt-2"
                                        :message="
                                            form.errors.proof_document_image
                                        "
                                    />
                                </div>

                                <PrimaryButton :disabled="form.processing"
                                    >Submit</PrimaryButton
                                >
                            </form>
                        </section>

                        <div
                            v-if="service !== null && !service.verified_at"
                            class="absolute top-0 left-0 flex items-start justify-center w-full h-full pt-24 backdrop-blur-sm"
                        >
                            <div class="h-auto p-4 bg-white rounded-md">
                                Application sent! Please wait for the approval.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
