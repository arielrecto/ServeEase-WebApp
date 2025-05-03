<script setup>
import { Head, Link, useForm, } from '@inertiajs/vue3';
import HeaderBackButton from '@/Components/HeaderBackButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/Form/SelectInput.vue';
import ImageUpload from '@/Components/Form/ImageUpload.vue';


defineProps(['serviceTypes', 'barangays']);

const form = useForm({
    name: null,
    description: null,
    price: null,
    priceType: null,
    termAndCondition: null,
    serviceType: null,
    barangay: null,
    thumbnail: null,
    is_quantifiable: false, // Add this
});

const submit = () => {
    form.post(route('service-provider.services.store'), {
        onFinish: () => form.reset(),
    });
}
</script>

<template>

    <Head title="Add service" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('service-provider.services.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Add service</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-gray-600">Fill out the necessary fields.
                        </p>

                        <section class="max-w-xl">
                            <form @submit.prevent="submit" class="mt-6 space-y-6">
                                <!-- <div>
                                    <InputLabel for="service" value="Select a service that you offer" />

                                    <SelectInput id="service" :choices="serviceTypes" class="block w-full mt-1"
                                        v-model="form.service" required />

                                    <InputError class="mt-2" :message="form.errors.service" />
                                </div> -->


                                <div>
                                    <InputLabel for="name" value="Name of service" />

                                    <TextInput id="name" type="text" class="block w-full mt-1" v-model="form.name"
                                        autofocus required />

                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div>
                                    <InputLabel for="price" value="Price" />

                                    <TextInput id="name" type="number" class="block w-full mt-1" v-model="form.price"
                                        autofocus required />

                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <div class="space-y-4">
                                    <!-- Quantifiable Checkbox -->
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            id="is_quantifiable"
                                            v-model="form.is_quantifiable"
                                            class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                                        />
                                        <label for="is_quantifiable" class="ml-2 text-sm text-gray-700">
                                           Is Quantifiable
                                        </label>
                                    </div>

                                    <!-- Quantity Input (shown only when is_quantifiable is true) -->
                
                                </div>

                                <div>
                                    <InputLabel for="" value="Price Type" />

                                    <select class="select select-bordered w-full" v-model="form.priceType">
                                        <option disabled selected>Select Price Type</option>

                                        <option value="hr">Hourly</option>
                                        <option value="day">Day(8hrs)</option>
                                        <option value="fixed rate">Fixed Rate</option>



                                    </select>

                                    <!-- <TextInput id="description" type="text" class="block w-full mt-1" v-model="form.description"
                                        required /> -->

                                    <InputError class="mt-2" :message="form.errors.service_type" />
                                </div>



                                <div>
                                    <InputLabel for="description" value="Description" />

                                    <textarea id="description" v-model="form.description"
                                        class="block h-32 w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary resize-none"
                                        required></textarea>

                                    <!-- <TextInput id="description" type="text" class="block w-full mt-1" v-model="form.description"
                                        required /> -->

                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>


                                <div>
                                    <InputLabel for="description" value="Term & Condition" />

                                    <textarea id="description" v-model="form.termAndCondition"
                                        class="block h-32 w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary resize-none"
                                        required></textarea>

                                    <!-- <TextInput id="description" type="text" class="block w-full mt-1" v-model="form.description"
                                        required /> -->

                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <div>
                                    <InputLabel for="Service Type" value="Service Type" />

                                    <select class="select select-bordered w-full" v-model="form.serviceType">
                                        <option disabled selected>Select Service Type</option>
                                        <template v-for="serviceType in serviceTypes" :key="serviceType?.id">
                                            <option :value="serviceType.id">{{ serviceType?.name }} </option>
                                        </template>


                                    </select>

                                    <!-- <TextInput id="description" type="text" class="block w-full mt-1" v-model="form.description"
                                        required /> -->

                                    <InputError class="mt-2" :message="form.errors.service_type" />
                                </div>


                                <div>
                                    <InputLabel for="Barangay" value="Barangay" />

                                    <select class="select select-bordered w-full" v-model="form.barangay">
                                        <option disabled selected>Select Barangay</option>
                                        <template v-for="barangay in barangays" :key="barangay?.id">
                                            <option :value="barangay.id">{{ barangay?.name }} </option>
                                        </template>

                                    </select>

                                    <!-- <TextInput id="description" type="text" class="block w-full mt-1" v-model="form.description"
                                        required /> -->

                                    <InputError class="mt-2" :message="form.errors.service_type" />
                                </div>




                                <div>
                                    <InputLabel for="thumbnail" value="Thumbnail" />

                                    <ImageUpload @success="(src) => form.thumbnail = src" />

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
