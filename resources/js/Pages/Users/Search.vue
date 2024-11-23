<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";

import { useLoader } from "@/Composables/loader";

import Header from "@/Components/Guest/Header.vue";
import Footer from "@/Components/Footer.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import ComboBox from "@/Components/Form/ComboBox.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";

defineProps(["brgys", "services"]);

const ratingFilterOptions = ["Highest", "Lowest"];
const priceFilterOptions = ["High", "Low"];
const transactionFilterOptions = ["High", "Low"];

const form = useForm({
    service: "",
    brgy: "",
    byRating: "",
    byPrice: "",
    byTransaction: "",
});
const more = ref(15);
const userServices = ref([]);
const dataContainer = ref(null);

const { setIsLoading } = useLoader();

const fetchServices = async () => {
    try {
        const res = await axios.get(route("search.filter"), {
            params: {
                service: form.service,
                brgy: form.brgy,
                byRating: form.byRating,
                byPrice: form.byPrice,
                byTransaction: form.byTransaction,
            },
        });

        if (res.status === 200) {
            userServices.value = res.data.services;
        }
    } catch (error) {
        console.error(error);
    }
};

const submit = async () => {
    setIsLoading(true);
    await fetchServices();
    setIsLoading(false);
};

onMounted(() => {
    // console.log(dataContainer.value.scrollHeight);

    let prevBottom = 0;

    dataContainer.value.addEventListener("scroll", async () => {
        const limit = 15;
        const containerHeight = dataContainer.value.scrollHeight;
        const containerBottom =
            dataContainer.value.scrollTop + dataContainer.value.offsetHeight;

        // console.log(
        //     "scroll height",
        //     containerHeight,
        //     "bottom",
        //     containerBottom
        // );

        if (containerBottom === prevBottom) return;

        if (containerBottom >= containerHeight - 2) {
            prevBottom = containerBottom;

            try {
                const res = await axios.get(route("search.filter"), {
                    params: {
                        more: more.value,
                        service: form.service,
                        brgy: form.brgy,
                        byRating: form.byRating,
                        byPrice: form.byPrice,
                        byTransaction: form.byTransaction,
                    },
                });

                if (res.status === 200) {
                    userServices.value.push(...res.data.services);

                    more.value += limit;
                }
            } catch (error) {
                console.error(error);
            }
        }
    });
});
</script>

<template>
    <Head title="Search a service" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <!-- <HeaderBackButton :url="route('admin.barangays.index')" /> -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Search a service
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">
                <section class="px-20 space-y-4">
                    <form @submit.prevent="submit" method="get">
                        <div class="flex items-end w-full gap-4">
                            <div class="w-full">
                                <InputLabel
                                    for="name"
                                    value="Select a service"
                                />
                                <ComboBox
                                    :items="services"
                                    identifier="name"
                                    valueName="name"
                                    keyName="id"
                                    @update:model-value="
                                        (value) =>
                                            (form.service = value.toLowerCase())
                                    "
                                    :isRequired="true"
                                    :class="`block w-full bg-white`"
                                />
                            </div>
                            <div class="w-full">
                                <InputLabel
                                    for="name"
                                    value="Select a barangay"
                                />
                                <ComboBox
                                    :items="brgys"
                                    identifier="name"
                                    valueName="id"
                                    keyName="id"
                                    @update:model-value="
                                        (value) => (form.brgy = value)
                                    "
                                    :isRequired="true"
                                    :class="`block w-full bg-white`"
                                />
                            </div>
                            <div>
                                <PrimaryButton>Go</PrimaryButton>
                            </div>
                        </div>
                    </form>

                    <div
                        v-if="userServices.length > 0"
                        class="flex flex-wrap items-center justify-center gap-4"
                    >
                        <div class="flex items-center gap-x-2">
                            <InputLabel for="byRating" value="Rating" />

                            <SelectInput
                                id="byRating"
                                @update-value="fetchServices"
                                class="block w-full mt-1"
                                v-model="form.byRating"
                                required
                            >
                                <option
                                    v-for="item in ratingFilterOptions"
                                    :value="item"
                                >
                                    {{ item }}
                                </option>
                            </SelectInput>

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                        </div>
                        <div class="flex items-center gap-x-2">
                            <InputLabel for="byPrice" value="Price" />

                            <SelectInput
                                id="byPrice"
                                @update-value="fetchServices"
                                class="block w-full mt-1"
                                v-model="form.byPrice"
                                required
                            >
                                <option
                                    v-for="item in priceFilterOptions"
                                    :value="item"
                                >
                                    {{ item }}
                                </option>
                            </SelectInput>

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                        </div>
                        <div class="flex items-center gap-x-2">
                            <InputLabel
                                for="byTransaction"
                                value="No. of transactions"
                            />

                            <SelectInput
                                id="byTransaction"
                                @update-value="fetchServices"
                                class="block w-full mt-1"
                                v-model="form.byTransaction"
                                required
                            >
                                <option
                                    v-for="item in transactionFilterOptions"
                                    :value="item"
                                >
                                    {{ item }}
                                </option>
                            </SelectInput>

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                        </div>
                    </div>
                </section>

                <section class="px-20">
                    <div
                        ref="dataContainer"
                        class="grid grid-cols-1 gap-10 mb-4 overflow-y-auto justify-items-center sm:grid-cols-2 max-h-[70vh]"
                    >
                        <article
                            v-for="item in userServices"
                            class="flex items-center w-full gap-x-2 group"
                        >
                            <div
                                class="h-32 overflow-hidden aspect-video rounded-xl"
                            >
                                <img
                                    alt=""
                                    :src="
                                        item.thumbnail ??
                                        'https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'
                                    "
                                    class="object-cover w-full h-full"
                                />
                            </div>

                            <div class="w-full">
                                <div
                                    class="flex items-center justify-between w-full gap-x-2"
                                >
                                    <h3
                                        :title="item.name"
                                        class="text-lg font-medium text-gray-900 line-clamp-1 text-ellipsis"
                                    >
                                        <a href="#">{{ item.name }}</a>
                                    </h3>

                                    <span class="text-sm">
                                        <i
                                            class="text-yellow-500 fa-solid fa-star"
                                        ></i>
                                        4.8
                                    </span>
                                </div>

                                <div class="mt-1">{{ item.user.name }}</div>

                                <div
                                    class="flex justify-between mt-1 text-gray-500 line-clamp-3 text-sm/relaxed"
                                >
                                    <p>
                                        ₱ {{ item.price }}
                                        <span v-if="item.price_type === 'fixed'"
                                            >(Fixed)</span
                                        >
                                    </p>
                                    <span>0 transaction(s)</span>
                                </div>
                            </div>
                        </article>
                        <!-- <article v-for="n in 20" class="w-64 group">
                        <img
                            alt=""
                            src="https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80"
                            class="object-cover transition shadow-xl rounded-xl aspect-square"
                        />

                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <a href="#">
                                    <h3
                                        class="text-lg font-medium text-gray-900"
                                    >
                                        John S. Doe
                                    </h3>
                                </a>

                                <span class="text-sm"
                                    ><i
                                        class="text-yellow-500 fa-solid fa-star"
                                    ></i>
                                    4.8</span
                                >
                            </div>

                            <p
                                class="mt-2 text-gray-500 line-clamp-3 text-sm/relaxed"
                            >
                                ₱ 120/hr
                            </p>
                        </div>
                    </article> -->
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
