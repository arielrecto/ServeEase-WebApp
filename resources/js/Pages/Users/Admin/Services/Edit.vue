<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import HeaderBackButton from '@/Components/HeaderBackButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ImageUpload from '@/Components/Form/ImageUpload.vue';

const props = defineProps({
    user: Object,
    service: {
        type: Object,
        required: true
    },
    barangays: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: props.service.name,
    description: props.service.description,
    price: props.service.price,
    price_type: props.service.price_type,
    terms_and_conditions: props.service.terms_and_conditions,
    barangay_id: props.service.barangay_id,
    thumbnail: null
});

const priceTypes = [
    { value: 'hr', label: 'Per Hour' },
    { value: 'day', label: 'Per Day (8hrs)' },
    { value: 'fixed', label: 'Fixed Rate' }
];

const submit = () => {
    form.put(route('admin.services.update', props.service.id));
};
</script>

<template>

    <Head title="Edit Service" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('admin.service-provider.show', user.id)" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Service</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-gray-600">Update your service information below.</p>

                        <section class="max-w-xl">
                            <form @submit.prevent="submit" class="mt-6 space-y-6">
                                <!-- Basic Information -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>

                                    <div>
                                        <InputLabel for="name" value="Service Name" />
                                        <TextInput id="name" type="text" class="block w-full mt-1" v-model="form.name"
                                            required />
                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>

                                    <div>
                                        <InputLabel for="description" value="Description" />
                                        <textarea id="description" v-model="form.description" rows="4"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary"
                                            required></textarea>
                                        <InputError class="mt-2" :message="form.errors.description" />
                                    </div>
                                </div>

                                <!-- Pricing Information -->
                                <div class="pt-4 space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">Pricing Information</h3>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <InputLabel for="price" value="Price" />
                                            <TextInput id="price" type="number" class="block w-full mt-1"
                                                v-model="form.price" min="0" step="0.01" required />
                                            <InputError class="mt-2" :message="form.errors.price" />
                                        </div>

                                        <div>
                                            <InputLabel for="price_type" value="Price Type" />
                                            <select id="price_type" v-model="form.price_type"
                                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary"
                                                required>
                                                <option v-for="type in priceTypes" :key="type.value"
                                                    :value="type.value">
                                                    {{ type.label }}
                                                </option>
                                            </select>
                                            <InputError class="mt-2" :message="form.errors.price_type" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="pt-4 space-y-4">
                                    <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>

                                    <div>
                                        <InputLabel for="terms_and_conditions" value="Terms and Conditions" />
                                        <textarea id="terms_and_conditions" v-model="form.terms_and_conditions" rows="4"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary"
                                            required></textarea>
                                        <InputError class="mt-2" :message="form.errors.terms_and_conditions" />
                                    </div>

                                    <div>
                                        <InputLabel for="barangay" value="Service Location (Barangay)" />
                                        <select id="barangay" v-model="form.barangay_id"
                                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary"
                                            required>
                                            <option value="" disabled>Select a barangay</option>
                                            <option v-for="barangay in barangays" :key="barangay.id"
                                                :value="barangay.id">
                                                {{ barangay.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.barangay_id" />
                                    </div>

                                    <div>
                                        <InputLabel for="thumbnail" value="Service Thumbnail" />
                                        <div v-if="service.thumbnail" class="mt-2">
                                            <img :src="service.thumbnail" alt="Current thumbnail"
                                                class="object-cover w-32 h-32 rounded" />
                                        </div>
                                        <div class="mt-2">
                                            <ImageUpload @success="(src) => form.thumbnail = src" :required="false"
                                                class="mt-1" />
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">Leave empty to keep the current thumbnail
                                        </p>
                                        <InputError class="mt-2" :message="form.errors.thumbnail" />
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    <PrimaryButton :disabled="form.processing">Save Changes</PrimaryButton>

                                    <span v-show="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</span>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
