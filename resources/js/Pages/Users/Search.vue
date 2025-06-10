<script setup>
import { Head, useForm, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
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
import SearchForm from "@/Components/Form/SearchForm.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import UserServiceCard from "@/Components/UserServiceCard.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    brgys: Array,
    services: Array,
    filters: Object,
});

// Initialize form with URL query parameters
const form = useForm({
    search: new URL(window.location.href).searchParams.get("search") || "",
    service: new URL(window.location.href).searchParams.get("service") || "",
    brgy: new URL(window.location.href).searchParams.get("brgy") || "",
    byRating: new URL(window.location.href).searchParams.get("byRating") || "",
    byPrice: new URL(window.location.href).searchParams.get("byPrice") || "",
    byTransaction:
        new URL(window.location.href).searchParams.get("byTransaction") || "",
});

const selectedCategory = computed(() => {
    if (!form.service) return null;

    return props.services.find((service) => service.id === Number(form.service))
        .name;
});

const ratingFilterOptions = ["Highest", "Lowest"];
const priceFilterOptions = ["High", "Low"];
const transactionFilterOptions = ["High", "Low"];

const more = ref(15);
const userServices = ref([]);
const dataContainer = ref(null);
const searchMode = ref("keyword"); // 'keyword' or 'advanced'

const toggleSearchMode = (mode) => {
    searchMode.value = mode;
    resetSearch();
};

const resetSearch = async () => {
    form.reset();
    // Explicitly clear all values after form.reset()
    form.search = "";
    // form.service = "";
    form.brgy = "";
    form.byRating = "";
    form.byPrice = "";
    form.byTransaction = "";
    await fetchServices();
};

const { setIsLoading } = useLoader();

const fetchServices = async () => {
    try {
        // Update URL with current filters
        const queryParams = new URLSearchParams();
        Object.entries(form.data()).forEach(([key, value]) => {
            if (value) queryParams.append(key, value);
        });

        const newUrl = `${window.location.pathname}?${queryParams.toString()}`;
        window.history.pushState({}, "", newUrl);

        const res = await axios.get(route("search.filter"), {
            params: {
                ...form.data(),
                authId: usePage().props.auth.user.id,
            },
        });

        if (res.status === 200) {
            userServices.value = res.data.services;
        }
    } catch (error) {
        console.error(error);
    }
};

// Add reset function
const resetFilters = () => {
    form.reset();
    // Explicitly clear all values after form.reset()
    form.search = "";
    form.service = "";
    form.brgy = "";
    form.byRating = "";
    form.byPrice = "";
    form.byTransaction = "";

    // Update URL to remove query params
    router.get(
        route("search.index"),
        {},
        {
            preserveState: false,
            preserveScroll: true,
            replace: true,
        }
    );
    fetchServices();
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
                                    @click="resetFilters"
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
                                    <div class="flex justify-center">
                                        <!-- <div class="space-y-2">
                                            <InputLabel
                                                for="service"
                                                value="Select a service"
                                            />
                                            <ComboBox
                                                :selected-item="
                                                    selectedCategory
                                                "
                                                :items="services"
                                                identifier="name"
                                                valueName="id"
                                                keyName="id"
                                                @update:model-value="
                                                    (value) =>
                                                        (form.service = value)
                                                "
                                                @reset-value="form.service = ''"
                                                :isRequired="false"
                                                :class="`block w-full bg-white border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm`"
                                            />
                                        </div> -->
                                        <div class="w-full space-y-2">
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
                                                    (value) =>
                                                        (form.brgy = value)
                                                "
                                                @reset-value="form.brgy = ''"
                                                :isRequired="false"
                                                :class="`block w-full bg-white border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm`"
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
                                                    <option value="">
                                                        All
                                                    </option>
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

                                        <PrimaryButton
                                            type="submit"
                                            class="px-6"
                                        >
                                            Apply Filters
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- <div
                        v-if="userServices.length > 0"
                        class="flex flex-col flex-wrap items-center justify-center gap-4 lg:flex-row"
                    >
                        <div
                            class="flex items-center flex-1 w-full lg:w-auto gap-x-2"
                        >
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

                        </div>
                        <div
                            class="flex items-center flex-1 w-full lg:w-auto gap-x-2"
                        >
                            <InputLabel
                                for="byTransaction"
                                value="Popularity"
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

                        </div>
                        <div
                            class="flex items-center flex-1 w-full lg:w-auto gap-x-2"
                        >
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

                        </div>
                    </div> -->
                </section>

                <section class="px-20">
                    <div
                        ref="dataContainer"
                        class="grid grid-cols-1 gap-10 mb-4 overflow-y-auto justify-items-center md:grid-cols-2 max-h-[70vh]"
                    >
                        <template v-if="userServices.length > 0">
                            <UserServiceCard
                                v-for="item in userServices"
                                :service="item"
                            />
                        </template>
                        <template v-else>
                            <div
                                class="flex flex-col items-center justify-center col-span-2 py-12"
                            >
                                <i
                                    class="mb-4 text-4xl text-gray-400 fas fa-search"
                                ></i>
                                <p class="text-lg font-medium text-gray-600">
                                    No services found
                                </p>
                                <p class="mt-2 text-sm text-gray-500">
                                    Try adjusting your search criteria or
                                    filters
                                </p>
                            </div>
                        </template>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
