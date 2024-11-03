<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import Header from '@/Components/Guest/Header.vue';
import Footer from '@/Components/Footer.vue';
import HeaderBackButton from '@/Components/HeaderBackButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ComboBox from '@/Components/Form/ComboBox.vue';
import SelectInput from '@/Components/Form/SelectInput.vue';

defineProps(['brgys', 'services', 'canLogin', 'canRegister']);

const ratingFilterOptions = ['Highest', 'Lowest'];
const priceFilterOptions = ['High', 'Low'];
const transactionFilterOptions = ['High', 'Low'];

const form = useForm({
    service: '',
    brgy: '',
    byRating: '',
    byPrice: '',
    byTransaction: '',
});

const submitQuery = () => {
    // form.get(route(''),{

    // });
    console.log('hello');

}
</script>

<template>

    <Head title="Search a service" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <!-- <HeaderBackButton :url="route('admin.barangays.index')" /> -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Search a service</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-10">
                <section class="px-20 space-y-4">
                    <form @submit.prevent="submitQuery" method="get">
                        <div class="flex items-end w-full gap-4">
                            <div class="w-full">
                                <InputLabel for="name"
                                value="Select a service" />
                                <ComboBox :items="services" identifier="name" keyName="id" @update:model-value="(value) => form.brgy = value" :class="`block w-full bg-white`" />
                            </div>
                            <div class="w-full">
                                <InputLabel for="name"
                                value="Select a barangay" />
                                <ComboBox :items="brgys" identifier="name" keyName="id" @update:model-value="(value) => form.service = value" :class="`block w-full bg-white`" />
                            </div>
                            <!-- <span>{{ form.brgy }} {{ form.service }} {{ form.byRating }} {{ form.byPrice }} {{ form.byTransaction }}</span> -->
                            <div>
                                <PrimaryButton>Go</PrimaryButton>
                            </div>
                        </div>
                    </form>

                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <div class="flex items-center gap-x-2">
                            <InputLabel for="byRating" value="Rating" />

                            <SelectInput id="byRating" class="block w-full mt-1" v-model="form.byRating" required>
                                <option v-for="item in ratingFilterOptions" :value="item">{{ item }}</option>
                            </SelectInput>

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                        </div>
                        <div class="flex items-center gap-x-2">
                            <InputLabel for="byPrice" value="Price" />

                            <SelectInput id="byRating" class="block w-full mt-1" v-model="form.byPrice" required>
                                <option v-for="item in priceFilterOptions" :value="item">{{ item }}</option>
                            </SelectInput>

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                        </div>
                        <div class="flex items-center gap-x-2">
                            <InputLabel for="byTransaction" value="No. of transactions" />

                            <SelectInput id="byTransaction" class="block w-full mt-1" v-model="form.byTransaction" required>
                                <option v-for="item in transactionFilterOptions" :value="item">{{ item }}</option>
                            </SelectInput>

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                        </div>
                    </div>
                </section>

                <section class="px-20">
                    <div class="grid grid-cols-1 gap-10 mb-4 overflow-y-auto justify-items-center sm:grid-cols-3">
                        <article v-for="n in 6" class="w-64 group">
                            <img
                            alt=""
                            src="https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80"
                            class="rounded-xl object-cover shadow-xl aspect-square transition"
                            />

                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <a href="#">
                                        <h3 class="text-lg font-medium text-gray-900">John S. Doe</h3>
                                    </a>

                                    <span class="text-sm"><i class="text-yellow-500 fa-solid fa-star"></i> 4.8</span>
                                </div>

                                <div class="mt-2 flex text-gray-500 justify-between items-center">
                                    <p class="line-clamp-3 text-sm/relaxed">
                                        ₱ 120/hr
                                    </p>

                                    <span class="inline-flex items-center gap-x-2 text-sm">
                                        <!-- <i class="fi fi-rr-receipt"></i> -->
                                        0 transaction(s)
                                    </span>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
