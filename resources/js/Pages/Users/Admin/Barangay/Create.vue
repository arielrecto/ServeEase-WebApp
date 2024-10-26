<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import HeaderBackButton from '@/Components/HeaderBackButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm({
    name: null,
});

const addBrgy = () => {
    form.post(route('admin.barangays.store'), {
        onFinish: () => form.reset(),
    });
}
</script>

<template>

    <Head title="Add brgy." />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('admin.barangays.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Add brgy.</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-gray-600">Fill out the necessary fields.
                        </p>

                        <section class="max-w-xl">
                            <form @submit.prevent="addBrgy" class="mt-6 space-y-6">
                                <div>
                                    <InputLabel for="name"
                                        value="Brgy. name" />

                                    <TextInput id="name" type="text" class="block w-full mt-1"
                                        v-model="form.name" autofocus required />

                                    <InputError class="mt-2" :message="form.errors.name" />
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
