<script setup>
import { reactive, ref, computed } from "vue";
import moment from "moment";
import { Head, usePage, Link } from "@inertiajs/vue3";
import axios from "axios";

import SelectInput from "@/Components/Form/SelectInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

import { useLoader } from "../../Composables/loader";

const page = usePage();
const authUser = page.props.auth.user;

const props = defineProps([
    "providerProfile",
    "feedbackCount",
    "user",
    "profile",
    "services",
    'feedbacks',
    'finishedBookingCount',
    'totalBookingCount'
]);

const { setIsLoading } = useLoader();

// Dummy data for services
// const services = ref([
//     {
//         id: 1,
//         name: "Home Cleaning Service",
//         rating: 4.5,
//         reviewCount: 28,
//         thumbnail: "/assets/images/default-service.jpg"
//     },
//     {
//         id: 2,
//         name: "Garden Maintenance",
//         rating: 4.8,
//         reviewCount: 15,
//         thumbnail: "/assets/images/default-service.jpg"
//     }
// ]);

// Dummy data for reviews
// const reviews = ref([
//     {
//         id: 1,
//         serviceName: "Home Cleaning Service",
//         rating: 5,
//         comment: "Excellent service, very thorough and professional",
//         date: "2025-04-15",
//         reviewer: "John Doe"
//     },
//     {
//         id: 2,
//         serviceName: "Garden Maintenance",
//         rating: 4,
//         comment: "Good work, but could be more detailed",
//         date: "2025-04-14",
//         reviewer: "Jane Smith"
//     }
// ]);

// Filter state
const reviewFilter = ref({
    rating: null,
    serviceName: null
});

const filteredReviews = computed(() => {
    return props.feedbacks.filter(review => {
        if (reviewFilter.value.rating && review.rate !== reviewFilter.value.rating) {
            return false;
        }
        if (reviewFilter.value.serviceName && review.serviceName !== reviewFilter.value.serviceName) {
            return false;
        }
        return true;
    });
});

const state = reactive({
    tabs: [
        { name: "Services", value: "0" },
        { name: "Reviews", value: "1" },
    ],
});

const ratingOptions = [5, 4, 3, 2, 1];
</script>

<template>

    <Head title="Provider Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Provider Profile</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div class="flex flex-col items-start justify-between gap-5 md:flex-row">
                        <div class="flex items-center gap-x-6">
                            <div class="w-16 h-16 overflow-hidden bg-gray-300 rounded-full md:w-28 md:h-28">
                                <img :src="user?.profile?.user_avatar" class="object-cover w-full h-full"
                                    alt="Profile Photo" />
                            </div>
                            <div class="space-y-1">
                                <div class="text-2xl font-bold">{{ user?.profile?.full_name }}</div>
                                <div class="text-sm text-gray-600">
                                    <i class="ri-phone-line"></i> {{ providerProfile?.contact }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="ri-time-line"></i> {{ providerProfile?.experience }} {{
                                        providerProfile?.experience_duration }} Experience
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="ri-tools-line"></i> {{ providerProfile?.serviceType?.name }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-x-4">
                            <div class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72">
                                <div class="text-gray-600">Finished Bookings</div>
                                <div class="text-2xl font-black text-primary">
                                    {{ finishedBookingCount ?? "0" }}
                                </div>
                            </div>

                            <div class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72">
                                <div class="text-gray-600">Total Bookings</div>
                                <div class="text-2xl font-black text-primary">
                                    {{ totalBookingCount ?? "0" }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs Content -->
                <div class="bg-white shadow sm:rounded-lg">
                    <Tabs value="0">
                        <TabList class="border-b border-gray-200">
                            <Tab v-for="tab in state.tabs" :key="tab.value" :value="tab.value"
                                class="px-6 py-4 text-sm font-medium" v-slot="{ selected }">
                                <button
                                    :class="['focus:outline-none', selected ? 'text-primary border-b-2 border-primary' : 'text-gray-500']">
                                    {{ tab.name }}
                                </button>
                            </Tab>
                        </TabList>

                        <TabPanels class="p-6">
                            <!-- Services Tab -->
                            <TabPanel value="0">
                                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                    <div v-for="service in services" :key="service.id"
                                        class="overflow-hidden bg-white rounded-lg shadow">
                                        <img :src="service.service_thumbnail" :alt="service.name"
                                            class="object-cover w-full h-48" />
                                        <div class="p-4">
                                            <h3 class="mb-2 text-lg font-semibold">{{ service.name }}</h3>
                                            <div class="flex items-center mb-4">
                                                <i class="text-yellow-500 ri-star-fill"></i>
                                                <span class="ml-1">{{ service.avg_rate }}</span>
                                                <span class="ml-2 text-sm text-gray-500">({{ service.total_review_count
                                                }}
                                                    reviews)</span>
                                            </div>
                                            <Link :href="route('service-provider.services.edit', service.id)"
                                                class="inline-flex items-center text-sm text-primary hover:underline">
                                            <i class="mr-1 ri-edit-line"></i> Edit Service
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </TabPanel>

                            <!-- Reviews Tab -->
                            <TabPanel value="1">
                                <!-- Filters -->
                                <div class="flex gap-4 mb-6">
                                    <div class="w-48">
                                        <SelectInput v-model="reviewFilter.rating" class="w-full">
                                            <option value="">All Ratings</option>
                                            <option v-for="rating in ratingOptions" :key="rating" :value="rating">
                                                {{ rating }} Stars
                                            </option>
                                        </SelectInput>
                                    </div>
                                    <div class="w-64">
                                        <SelectInput v-model="reviewFilter.serviceName" class="w-full">
                                            <option value="">All Services</option>
                                            <option v-for="service in services" :key="service.id" :value="service.name">
                                                {{ service.name }}
                                            </option>
                                        </SelectInput>
                                    </div>
                                </div>

                                <!-- Reviews List -->
                                <div class="space-y-4">
                                    <div v-for="review in filteredReviews" :key="review.id"
                                        class="p-4 rounded-lg bg-gray-50">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-medium">{{ review.user.profile.full_name }}</div>
                                            <div class="text-sm text-gray-500">
                                                {{ moment(review.created_at).format('MMM D, YYYY') }}
                                            </div>
                                        </div>
                                        <div class="mb-2 text-sm text-gray-600">{{ review.serviceName }}</div>
                                        <div class="flex items-center mb-3">
                                            <div class="flex text-yellow-500">
                                                <i v-for="star in review.rate" :key="star" class="ri-star-fill"></i>
                                            </div>
                                        </div>
                                        <p class="text-gray-700">{{ review.content }}</p>
                                    </div>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
