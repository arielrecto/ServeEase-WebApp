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
import { ref, computed, onMounted } from "vue";

const activeTab = ref('form');

const props = defineProps({
    serviceTypes: Object,
    service: Object,
    providerProfile: Object,
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
    if (props.providerProfile === null) {
        console.log('create');

        form.post(route("customer.service-provider.store"), {
            onFinish: () => form.reset(),
        });

        return;
    }

    console.log('update');

    form.post(route("customer.service-provider.update", props.providerProfile?.id), {
        _method: 'put',
        onFinish: () => form.reset(),
    });
};

// Add computed property for application status
const applicationStatus = computed(() => {
    if (!props.providerProfile) return null;

    const statusMap = {
        'pending': { color: 'yellow', text: 'Pending Review' },
        'approved': { color: 'green', text: 'Approved' },
        'rejected': { color: 'red', text: 'Rejected' }
    };

    return statusMap[props.providerProfile.status] || { color: 'gray', text: 'Unknown' };
});

// Add new computed property for remarks display
const remarkClass = computed(() => (status) => {
    const classes = {
        'rejected': 'bg-red-50 border-red-200 text-red-700',
        'pending': 'bg-yellow-50 border-yellow-200 text-yellow-700',
        'approved': 'bg-green-50 border-green-200 text-green-700'
    };
    return classes[status] || 'bg-gray-50 border-gray-200 text-gray-700';
});

onMounted(() => {
    if (props.providerProfile) {
        activeTab.value = 'status';
    }
})
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
                    <!-- Add tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="flex -mb-px space-x-8">
                            <button v-if="providerProfile?.status !== 'pending'" @click="activeTab = 'form'" :class="[
                                activeTab === 'form'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                            ]">
                                Application Form
                            </button>
                            <button @click="activeTab = 'status'" :class="[
                                activeTab === 'status'
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                            ]">
                                Application Status
                            </button>
                        </nav>
                    </div>

                    <!-- Application Form Tab -->
                    <div v-if="activeTab === 'form'" class="relative p-6">
                        <p class="text-gray-600">
                            Fill out the form for your application for service
                            provider in <strong>ServEase</strong>. Make sure
                            that the information you provide are correct and
                            legitimate.
                        </p>

                        <section class="max-w-xl">
                            <form @submit.prevent="submit" class="mt-6 space-y-6">
                                <h2 class="font-bold">Provider Information</h2>
                                <div>
                                    <InputLabel for="service" value="Select the service that you offer" />

                                    <SelectInput id="service" :choices="serviceTypes" optionKey="name" valueKey="id"
                                        class="block w-full mt-1" v-model="form.service" required />

                                    <InputError class="mt-2" :message="form.errors.service" />
                                </div>

                                <div class="flex items-end gap-4">
                                    <div>
                                        <InputLabel for="experience"
                                            value="How many years of experience have you been offering your service?" />

                                        <div>
                                            <TextInput id="experience" type="number" class="block w-full mt-1"
                                                v-model="form.experience" min="1" required />

                                            <InputError class="mt-2" :message="form.errors.experience
                                                " />
                                        </div>
                                    </div>
                                    <div>
                                        <!-- <InputLabel
                                            for="experienceDuration"
                                            value="Select the valid ID that you will submit."
                                        /> -->

                                        <SelectInput id="experienceDuration" :choices="experienceDurationOptions"
                                            optionKey="name" valueKey="name" class="block w-full mt-1"
                                            v-model="form.experience_duration" required />

                                        <InputError class="mt-2" :message="form.errors.experience_duration
                                            " />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="contact"
                                        value="Provide a contact number. This will be visible to the customers." />

                                    <TextInput id="contact" type="text" class="block w-full mt-1" v-model="form.contact"
                                        required />

                                    <InputError class="mt-2" :message="form.errors.contact" />
                                </div>

                                <h2 class="!mt-10 font-bold">
                                    Submit Documents
                                </h2>

                                <div>
                                    <InputLabel for="service" value="Select the valid ID that you will submit." />

                                    <SelectInput id="service" :choices="validIdTypes" optionKey="name" valueKey="name"
                                        class="block w-full mt-1" v-model="form.valid_id_type" required />

                                    <InputError class="mt-2" :message="form.errors.validIdType" />
                                </div>

                                <div>
                                    <InputLabel for="valid_id_image" value="Upload an image of your chosen valid ID." />

                                    <ImageUpload @success="
                                        (src) => (form.valid_id_image = src)
                                    " />

                                    <InputError class="mt-2" :message="form.errors.valid_id_image" />
                                </div>

                                <div>
                                    <InputLabel for="service"
                                        value="Select the type of document you will submit to prove your Bacoor citizenship." />

                                    <SelectInput id="service" :choices="citizenshipDocumentTypes" optionKey="name"
                                        valueKey="name" class="block w-full mt-1"
                                        v-model="form.citizenship_document_type" required />

                                    <InputError class="mt-2" :message="form.errors
                                        .citizenship_document_type
                                        " />
                                </div>

                                <div>
                                    <InputLabel for="valid_id_image"
                                        value="Upload an image of your chosen citizenship document." />

                                    <ImageUpload @success="
                                        (src) =>
                                        (form.citizenship_document_image =
                                            src)
                                    " />

                                    <InputError class="mt-2" :message="form.errors
                                        .citizenship_document_image
                                        " />
                                </div>

                                <div>
                                    <InputLabel for="proof_document_image"
                                        value="Upload an image to prove your legitimacy as a provider of your service. (e.g. Certificate of Employment, TESDA Certificate etc.)" />

                                    <ImageUpload @success="
                                        (src) =>
                                        (form.proof_document_image =
                                            src)
                                    " />

                                    <InputError class="mt-2" :message="form.errors.proof_document_image
                                        " />
                                </div>

                                <PrimaryButton :disabled="form.processing">Submit</PrimaryButton>
                            </form>
                        </section>

                        <div v-if="providerProfile !== null"
                            class="absolute top-0 left-0 flex items-start justify-center w-full h-full pt-24 backdrop-blur-sm">
                            <div class="h-auto p-4 bg-white rounded-md">
                                Application sent! Please wait for the approval.

                                <!-- {{ providerProfile?.status !== 'pending') ||
                                    providerProfile?.status
                                    !== 'rejected' }} -->
                            </div>
                        </div>
                    </div>

                    <!-- Application Status Tab -->
                    <div v-else-if="activeTab === 'status'" class="p-6">
                        <div v-if="providerProfile" class="space-y-6">
                            <!-- Status Badge -->
                            <div class="flex items-center gap-x-3">
                                <span class="text-lg font-semibold">Status:</span>
                                <span
                                    :class="`px-3 py-1 rounded-full text-${applicationStatus.color}-700 bg-${applicationStatus.color}-100`">
                                    {{ applicationStatus.text }}
                                </span>
                            </div>

                            <div v-if="providerProfile.status === 'pending'" class="p-4 border rounded-lg"
                                :class="remarkClass(providerProfile.status)">
                                <div class="flex items-center justify-center">
                                    Waiting for approval...
                                </div>
                            </div>

                            <!-- Remarks Section -->
                            <div v-if="providerProfile.remarks?.length" class="mt-4">
                                <h3 class="mb-4 text-lg font-semibold">Application Remarks</h3>
                                <div class="space-y-4">
                                    <div v-for="remark in providerProfile.remarks" :key="remark.id"
                                        class="p-4 border rounded-lg" :class="remarkClass(providerProfile.status)">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="space-y-1">
                                                <p class="font-medium">{{ remark.content }}</p>
                                                <p class="text-sm text-gray-600">
                                                    By: {{ remark.user?.name }}
                                                </p>
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                {{ new Date(remark.created_at).toLocaleDateString() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Application Details -->
                            <div class="mt-6">
                                <h3 class="mb-4 font-semibold">Application Details</h3>
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <p class="text-sm text-gray-500">Service Type</p>
                                        <p class="font-medium">{{ providerProfile.service_type?.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Experience</p>
                                        <p class="font-medium">
                                            {{ providerProfile.experience }} {{ providerProfile.experience_duration }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Contact</p>
                                        <p class="font-medium">{{ providerProfile.contact }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Submitted On</p>
                                        <p class="font-medium">
                                            {{ new Date(providerProfile.created_at).toLocaleDateString() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="py-8 text-center text-gray-500">
                            No application found. Please submit an application first.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
