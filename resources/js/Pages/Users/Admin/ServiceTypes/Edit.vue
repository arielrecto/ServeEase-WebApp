<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import HeaderBackButton from '@/Components/HeaderBackButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/Form/SelectInput.vue';
import ImageUpload from '@/Components/Form/ImageUpload.vue';

const props = defineProps(['service']);

const form = useForm({
    name: props.service.name,
    description: props.service.description,
    thumbnail: undefined,
});

const updateService = () => {
    form.post(route('admin.service-types.update', props.service.id), {
        _method: 'put',
        onFinish: () => form.reset(),
    });
}
</script>

<template>

    <Head title="Update service" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('admin.service-types.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Update service</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-gray-600">Fill out the necessary fields.
                        </p>

                        <section class="max-w-xl">
                            <form @submit.prevent="updateService" class="mt-6 space-y-6">
                                <div>
                                    <InputLabel for="name"
                                        value="Name of service" />

                                    <TextInput id="name" type="text" class="block w-full mt-1"
                                        v-model="form.name" autofocus required />

                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="description"
                                        value="Description" />

                                    <textarea id="description" v-model="form.description" class="block h-32 w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary resize-none" required></textarea>

                                    <!-- <TextInput id="description" type="text" class="block w-full mt-1" v-model="form.description"
                                        required /> -->

                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <div>
                                    <InputLabel for="thumbnail"
                                        value="Thumbnail" />

                                    <ImageUpload @success="(src) => form.thumbnail = src" :required="false" />

                                    <InputError class="mt-2" :message="form.errors.thumbnail" />
                                </div>

                                <PrimaryButton :disabled="form.processing">Submit</PrimaryButton>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
