<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import Header from '@/Components/Guest/Header.vue';
import Footer from '@/Components/Footer.vue';
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
    <Head title="Services" />

    <div class="bg-gray-100">
        <Header :canLogin="canLogin" :canRegister="canRegister" />
        <main class="container min-h-screen py-8 mx-auto space-y-10">
            <h1 class="text-3xl font-bold text-center">Find a service that's right for you</h1>
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

                            <p class="mt-2 text-gray-500 line-clamp-3 text-sm/relaxed">
                                ₱ 120/hr
                            </p>
                        </div>
                    </article>
                </div>
            </section>
        </main>
        <Footer />
    </div>
</template>
