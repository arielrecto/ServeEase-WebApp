<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { ModalRoot } from "@inertiaui/modal-vue";
import axios from "axios";
import { ref, onMounted } from "vue";

import { useLoader } from "@/Composables/loader";

import Header from "@/Components/Guest/Header.vue";
import Footer from "@/Components/Footer.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import ComboBox from "@/Components/Form/ComboBox.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import Loader from "@/Components/Loader.vue";
import SearchForm from "@/Components/Form/SearchForm.vue";
import ModalLinkSlideover from "@/Components/Modal/ModalLinkSlideover.vue";
import UserServiceCard from "@/Components/UserServiceCard.vue";

defineProps(["brgys", "services", "canLogin", "canRegister"]);

const ratingFilterOptions = ["Highest", "Lowest"];
const priceFilterOptions = ["High", "Low"];
const transactionFilterOptions = ["High", "Low"];

const form = useForm({
    search: "",
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
                search: form.search,
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

onMounted(async () => {
    setIsLoading(true);
    await fetchServices();
    setIsLoading(false);

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
                        search: form.search,
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

    <Head title="Services" />

    <Loader />

    <div class="bg-gray-100">
        <Header :canLogin="canLogin" :canRegister="canRegister" />
        <main class="container min-h-screen py-8 mx-auto space-y-10">
            <h1 class="text-3xl font-bold text-center">
                Find a service that's right for you
            </h1>
            <section class="px-20 space-y-4">
                <div class="space-y-5">
                    <SearchForm @submitted="
                        async (query) => {
                            form.search = query;
                            await fetchServices();
                        }
                    " placeholder="Search a keyword" />
                    <div class="flex items-center w-full">
                        <hr class="flex-1 border-gray-300" />
                        <div class="mx-5 text-xs font-semibold text-gray-700">
                            OR
                        </div>
                        <hr class="flex-1 border-gray-300" />
                    </div>
                    <form @submit.prevent="submit" method="get">
                        <div class="flex items-end w-full gap-4">
                            <div class="w-full">
                                <InputLabel for="name" value="Select a service" />
                                <ComboBox :items="services" identifier="name" valueName="id" keyName="id"
                                    @update:model-value="
                                        (value) =>
                                            (form.service = value)
                                    " @reset-value="form.service = ''" :isRequired="false"
                                    :class="`block w-full bg-white`" />
                            </div>
                            <div class="w-full">
                                <InputLabel for="name" value="Select a barangay" />
                                <ComboBox :items="brgys" identifier="name" valueName="id" keyName="id"
                                    @update:model-value="
                                        (value) => (form.brgy = value)
                                    " @reset-value="form.brgy = ''" :isRequired="false"
                                    :class="`block w-full bg-white`" />
                            </div>
                            <div>
                                <PrimaryButton>Go</PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>

                <div v-if="userServices.length > 0" class="flex flex-wrap items-center justify-center gap-4">
                    <div class="flex items-center flex-1 gap-x-2">
                        <InputLabel for="byRating" value="Rating" />

                        <SelectInput id="byRating" @update:modelValue="
                            async () => {
                                form.byRating = value;
                                await fetchServices();
                            }
                        " class="block w-full mt-1" v-model="form.byRating" required>
                            <option v-for="item in ratingFilterOptions" :value="item">
                                {{ item }}
                            </option>
                        </SelectInput>

                        <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                    </div>
                    <div class="flex items-center flex-1 gap-x-2">
                        <InputLabel for="byTransaction" value="Popularity" />

                        <SelectInput id="byTransaction" @update:modelValue="
                            async () => {
                                form.byTransaction = value;
                                await fetchServices();
                            }
                        " class="block w-full mt-1" v-model="form.byTransaction" required>
                            <option v-for="item in transactionFilterOptions" :value="item">
                                {{ item }}
                            </option>
                        </SelectInput>

                        <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                    </div>
                    <div class="flex items-center flex-1 gap-x-2">
                        <InputLabel for="byPrice" value="Price" />

                        <SelectInput id="byPrice" @update:modelValue="
                            async () => {
                                form.byPrice = value;
                                await fetchServices();
                            }
                        " class="block w-full mt-1" v-model="form.byPrice" required>
                            <option v-for="item in priceFilterOptions" :value="item">
                                {{ item }}
                            </option>
                        </SelectInput>

                        <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                    </div>
                </div>
            </section>

            <section class="px-20">
                <div ref="dataContainer"
                    class="grid grid-cols-1 gap-y-10 gap-x-16 mb-4 overflow-y-auto justify-items-center sm:grid-cols-2 max-h-[70vh]">
                    <UserServiceCard v-for="item in userServices" :service="item" linkType="modal" />
                </div>

                <div v-if="userServices === 0" class="text-sm text-center text-gray-700">
                    No services found.
                </div>
            </section>
        </main>
        <Footer />
    </div>

    <ModalRoot />
</template>
