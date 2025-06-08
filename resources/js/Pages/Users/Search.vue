<script setup>
import { Head, useForm, Link, usePage } from "@inertiajs/vue3";
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

const ratingFilterOptions = ["Highest", "Lowest"];
const priceFilterOptions = ["High", "Low"];
const transactionFilterOptions = ["High", "Low"];

const more = ref(15);
const userServices = ref([]);
const dataContainer = ref(null);

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
    // Use Inertia router to remove query parameters
    router.get(
        route("search.index", {
            _query: {
                service:
                    form.service ||
                    new URL(window.location.href).searchParams.get("service") ||
                    "",
            },
        }),
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
                <section class="px-20 space-y-4">
                    <div class="space-y-5">
                        <div class="flex items-center justify-between">
                            <!-- <SearchForm
                                :initial-value="form.search"
                                @submitted="async (query) => {
                                    form.search = query;
                                    await fetchServices();
                                }"
                                placeholder="Search a keyword"
                            /> -->
                            <!-- Add Reset Button -->
                        </div>

                        <!-- <div class="flex items-center w-full">
                            <hr class="flex-1 border-gray-300" />
                            <div class="mx-5 text-xs font-semibold text-gray-700">
                                OR
                            </div>
                            <hr class="flex-1 border-gray-300" />
                        </div> -->
                        <form @submit.prevent="submit" method="get">
                            <div class="flex items-end w-full gap-4">
                                <!-- Add search input -->
                                <div class="w-full">
                                    <InputLabel
                                        for="search"
                                        value="Search by name"
                                    />
                                    <TextInput
                                        id="search"
                                        type="text"
                                        v-model="form.search"
                                        class="block w-full"
                                        placeholder="Search service name..."
                                    />
                                </div>

                                <!-- Existing service selector -->
                                <!-- <div class="w-full">
                                    <InputLabel for="name" value="Select a service" />
                                    <ComboBox
                                        :items="services"
                                        identifier="name"
                                        valueName="id"
                                        keyName="id"
                                        @update:model-value="(value) => (form.service = value)"
                                        @reset-value="form.service = ''"
                                        :isRequired="false"
                                        :class="`block w-full bg-white`"
                                    />
                                </div> -->

                                <!-- Existing barangay selector -->
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
                                        @reset-value="form.brgy = ''"
                                        :isRequired="false"
                                        :class="`block w-full bg-white`"
                                    />
                                </div>

                                <div class="flex items-center gap-2">
                                    <PrimaryButton>Search</PrimaryButton>
                                    <button
                                        v-if="
                                            form.search ||
                                            form.service ||
                                            form.brgy ||
                                            form.byRating ||
                                            form.byPrice ||
                                            form.byTransaction
                                        "
                                        type="button"
                                        @click="resetFilters"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                                    >
                                        <i class="mr-2 fas fa-undo"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div
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

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
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

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
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

                            <!-- <InputError class="mt-2" :message="form.errors.gender" /> -->
                        </div>
                    </div>
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
