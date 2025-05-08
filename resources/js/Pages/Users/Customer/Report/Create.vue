<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/Form/SelectInput.vue';

const props = defineProps({
    providers: Array
});

const form = useForm({
    user_id: '',
    complaint: '',
    type: '',
    attachments: []
});

const reportTypes = [
    { id: 'illegal_transaction', name: 'Illegal Transaction' },
    { id: 'unfinished_booking', name: 'Unfinished Booking' }
];

const submit = () => {
    form.post(route('customer.report.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head title="File a Complaint" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                File a Complaint
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="max-w-xl space-y-6">
                            <div>
                                <InputLabel for="user_id" value="Select Service Provider" />
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                    required
                                >
                                    <option value="">Select a provider</option>
                                    <option
                                        v-for="provider in providers"
                                        :key="provider.id"
                                        :value="provider.id"
                                    >
                                        {{ provider.name }} - {{ provider.service }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.user_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="type" value="Type of Complaint" />
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                    required
                                >
                                    <option value="">Select type</option>
                                    <option
                                        v-for="type in reportTypes"
                                        :key="type.id"
                                        :value="type.id"
                                    >
                                        {{ type.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.type" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="complaint" value="Complaint Details" />
                                <textarea
                                    id="complaint"
                                    v-model="form.complaint"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                                    placeholder="Please provide detailed information about your complaint..."
                                    required
                                ></textarea>
                                <InputError :message="form.errors.complaint" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="attachments" value="Supporting Documents (Optional)" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Upload any relevant documents or screenshots (max 10MB each)
                                </p>
                                <div class="mt-2">
                                    <div class="flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                                                <p class="mb-2 text-sm text-gray-500">
                                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                                </p>
                                                <p class="text-xs text-gray-500">Any file type (MAX. 10MB per file)</p>
                                            </div>
                                            <input
                                                id="attachments"
                                                type="file"
                                                class="hidden"
                                                multiple
                                                @change="form.attachments = $event.target.files"
                                            />
                                        </label>
                                    </div>

                                    <!-- File Preview -->
                                    <div v-if="form.attachments.length" class="mt-4 space-y-2">
                                        <div v-for="(file, index) in form.attachments"
                                            :key="index"
                                            class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"
                                        >
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-file text-gray-400"></i>
                                                <span class="text-sm truncate max-w-xs">{{ file.name }}</span>
                                                <span class="text-xs text-gray-500">
                                                    {{ (file.size / 1024).toFixed(1) }} KB
                                                </span>
                                            </div>
                                            <button
                                                type="button"
                                                @click="form.attachments = Array.from(form.attachments).filter((_, i) => i !== index)"
                                                class="text-gray-400 hover:text-red-500"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.attachments" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end gap-x-4">
                                <a
                                    :href="route('customer.report.index')"
                                    class="text-gray-500 hover:text-gray-700"
                                >
                                    Cancel
                                </a>
                                <PrimaryButton :disabled="form.processing">
                                    Submit Report
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
