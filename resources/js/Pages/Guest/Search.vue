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

const resetSearch = async () => {
    form.reset();
    // Explicitly clear all values after form.reset()
    form.search = "";
    form.service = "";
    form.brgy = "";
    form.byRating = "";
    form.byPrice = "";
    form.byTransaction = "";
    await fetchServices();
};

const more = ref(15);
const userServices = ref([]);
const dataContainer = ref(null);
const searchMode = ref("keyword"); // 'keyword' or 'advanced'

const toggleSearchMode = (mode) => {
    searchMode.value = mode;
    resetSearch();
};

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
            <section class="px-20 space-y-6">
                <!-- Search Options -->
                <div class="flex justify-center gap-4">
                    <button
                        @click="toggleSearchMode('keyword')"
                        class="px-6 py-3 text-sm font-medium transition-all duration-200 rounded-lg"
                        :class="
                            searchMode === 'keyword'
                                ? 'bg-indigo-600 text-white shadow-lg scale-105'
                                : 'bg-white text-gray-700 hover:bg-gray-50'
                        "
                    >
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                            Search by Keyword
                        </div>
                    </button>
                    <button
                        @click="toggleSearchMode('advanced')"
                        class="px-6 py-3 text-sm font-medium transition-all duration-200 rounded-lg"
                        :class="
                            searchMode === 'advanced'
                                ? 'bg-indigo-600 text-white shadow-lg scale-105'
                                : 'bg-white text-gray-700 hover:bg-gray-50'
                        "
                    >
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                                />
                            </svg>
                            Advanced Filters
                        </div>
                    </button>
                </div>

                <!-- Search Content -->
                <div class="relative space-y-2">
                    <!-- Keyword Search -->
                    <div
                        v-if="searchMode === 'keyword'"
                        class="p-6 bg-white rounded-lg shadow-sm"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <SearchForm
                                    :initial-value="form.search"
                                    @submitted="
                                        async (query) => {
                                            form.search = query;
                                            await fetchServices();
                                        }
                                    "
                                    placeholder="Search for service or provider..."
                                />
                            </div>
                            <PrimaryButton
                                @click="resetSearch"
                                class="px-4 py-2 button-ghost"
                                title="Reset search"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                    />
                                </svg>
                            </PrimaryButton>
                        </div>
                    </div>

                    <!-- Advanced Search Section -->
                    <div
                        v-if="searchMode === 'advanced'"
                        class="p-6 bg-white rounded-lg shadow-sm"
                    >
                        <form @submit.prevent="submit" method="get">
                            <div class="space-y-4">
                                <!-- Service and Location Selection -->
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div class="space-y-2">
                                        <InputLabel
                                            for="service"
                                            value="Select a service"
                                        />
                                        <ComboBox
                                            :items="services"
                                            identifier="name"
                                            valueName="id"
                                            keyName="id"
                                            @update:model-value="
                                                (value) =>
                                                    (form.service = value)
                                            "
                                            :initial-value="form.service"
                                            @reset-value="form.service = ''"
                                            :isRequired="false"
                                            :class="`block w-full bg-white`"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <InputLabel
                                            for="brgy"
                                            value="Select a barangay"
                                        />
                                        <ComboBox
                                            :items="brgys"
                                            identifier="name"
                                            valueName="id"
                                            keyName="id"
                                            :initial-value="form.brgy"
                                            @update:model-value="
                                                (value) => (form.brgy = value)
                                            "
                                            @reset-value="form.brgy = ''"
                                            :isRequired="false"
                                            :class="`block w-full bg-white`"
                                        />
                                    </div>
                                </div>

                                <!-- Filters Section -->
                                <div class="pt-4 border-t">
                                    <h3
                                        class="mb-4 text-sm font-medium text-gray-700"
                                    >
                                        Filter Results By:
                                    </h3>
                                    <div
                                        class="grid grid-cols-1 gap-4 md:grid-cols-3"
                                    >
                                        <div class="space-y-2">
                                            <InputLabel
                                                for="byRating"
                                                value="Rating"
                                            />
                                            <SelectInput
                                                id="byRating"
                                                v-model="form.byRating"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                @change="fetchServices"
                                            >
                                                <option value="">
                                                    All Ratings
                                                </option>
                                                <option
                                                    v-for="item in ratingFilterOptions"
                                                    :key="item"
                                                    :value="item"
                                                >
                                                    {{ item }}
                                                </option>
                                            </SelectInput>
                                        </div>

                                        <div class="space-y-2">
                                            <InputLabel
                                                for="byTransaction"
                                                value="Popularity"
                                            />
                                            <SelectInput
                                                id="byTransaction"
                                                v-model="form.byTransaction"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                @change="fetchServices"
                                            >
                                                <option value="">All</option>
                                                <option
                                                    v-for="item in transactionFilterOptions"
                                                    :key="item"
                                                    :value="item"
                                                >
                                                    {{ item }}
                                                </option>
                                            </SelectInput>
                                        </div>

                                        <div class="space-y-2">
                                            <InputLabel
                                                for="byPrice"
                                                value="Price"
                                            />
                                            <SelectInput
                                                id="byPrice"
                                                v-model="form.byPrice"
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                @change="fetchServices"
                                            >
                                                <option value="">
                                                    All Prices
                                                </option>
                                                <option
                                                    v-for="item in priceFilterOptions"
                                                    :key="item"
                                                    :value="item"
                                                >
                                                    {{ item }}
                                                </option>
                                            </SelectInput>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between pt-6 border-t"
                                >
                                    <PrimaryButton
                                        @click="resetSearch"
                                        type="button"
                                        class="button-ghost"
                                        title="Reset filters"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 mr-2"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                            />
                                        </svg>
                                        Reset Filters
                                    </PrimaryButton>

                                    <PrimaryButton type="submit" class="px-6">
                                        Apply Filters
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <section class="px-20">
                <div
                    ref="dataContainer"
                    class="grid grid-cols-1 gap-y-10 gap-x-16 mb-4 overflow-y-auto justify-items-center md:grid-cols-2 max-h-[70vh]"
                >
                    <UserServiceCard
                        v-for="item in userServices"
                        :service="item"
                        linkType="modal"
                    />
                </div>

                <div
                    v-if="userServices.length === 0"
                    class="text-sm text-center text-gray-700"
                >
                    No services found.
                </div>
            </section>
        </main>
        <Footer />
    </div>

    <ModalRoot />
</template>
