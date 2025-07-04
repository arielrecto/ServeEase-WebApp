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
import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";

import { useLoader } from "../../Composables/loader";

const page = usePage();
const authUser = page.props.auth.user;

const props = defineProps([
    "providerProfile",
    "feedbackCount",
    "user",
    "profile",
    "services",
    "feedbacks",
    "finishedBookingCount",
    "totalBookingCount",
]);

const { setIsLoading } = useLoader();

// Filter state
const reviewFilter = ref({
    rating: null,
    serviceName: null,
});

const filteredReviews = computed(() => {
    return props.feedbacks.filter((review) => {
        // Check rating filter
        if (
            reviewFilter.value.rating &&
            parseInt(review.rate) !== parseInt(reviewFilter.value.rating)
        ) {
            return false;
        }

        // Check service name filter
        if (
            reviewFilter.value.serviceName &&
            review.avail_service.service.name !== reviewFilter.value.serviceName
        ) {
            return false;
        }

        return true;
    });
});

const state = reactive({
    tabs: [
        { name: "Services", value: "0", hidden: false },
        {
            name: "Archived Services",
            value: "1",
            hidden: usePage().props.auth.user.id !== props.user.id,
        },
        { name: "Reviews", value: "2", hidden: false },
    ],
});

const archivedServices = computed(() => {
    return props.services.filter((service) => service.archived_at !== null);
});

const activeServices = computed(() => {
    return props.services.filter((service) => service.archived_at === null);
});

const ratingOptions = [5, 4, 3, 2, 1];
</script>

<template>
    <Head title="Provider Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Provider Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <!-- Profile Header -->
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div
                        class="flex flex-col items-start justify-between gap-5 md:flex-row"
                    >
                        <div class="flex items-center gap-x-6">
                            <div
                                class="w-16 h-16 overflow-hidden bg-gray-300 rounded-full md:w-28 md:h-28"
                            >
                                <img
                                    :src="user?.profile?.user_avatar"
                                    class="object-cover w-full h-full"
                                    alt="Profile Photo"
                                />
                            </div>
                            <div class="space-y-1">
                                <div class="text-2xl font-bold">
                                    {{ user?.profile?.full_name }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="ri-phone-line"></i>
                                    {{ providerProfile?.contact }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="ri-time-line"></i>
                                    {{ providerProfile?.experience }}
                                    {{ providerProfile?.experience_duration }}
                                    Experience
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="ri-tools-line"></i>
                                    {{ providerProfile?.service_type?.name }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center w-full lg:w-auto gap-x-4">
                            <div
                                class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72"
                            >
                                <div class="text-gray-600">
                                    Finished Bookings
                                </div>
                                <div class="text-2xl font-black text-primary">
                                    {{ finishedBookingCount ?? "0" }}
                                </div>
                            </div>

                            <div
                                class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72"
                            >
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
                            <Tab
                                v-for="tab in state.tabs"
                                :key="tab.value"
                                :value="tab.value"
                                class="px-6 py-4 text-sm font-medium"
                                :class="[tab.hidden ? 'hidden' : '']"
                                v-slot="{ selected }"
                            >
                                <button
                                    :class="[
                                        'focus:outline-none',
                                        selected
                                            ? 'text-primary border-b-2 border-primary'
                                            : 'text-gray-500',
                                    ]"
                                >
                                    {{ tab.name }}
                                </button>
                            </Tab>
                        </TabList>

                        <TabPanels class="p-6">
                            <!-- Services Tab -->
                            <TabPanel value="0">
                                <div
                                    v-if="activeServices.length > 0"
                                    class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
                                >
                                    <div
                                        v-for="service in activeServices"
                                        :key="service.id"
                                        class="overflow-hidden bg-white rounded-lg shadow"
                                    >
                                        <img
                                            :src="service.service_thumbnail"
                                            :alt="service.name"
                                            class="object-cover w-full h-48"
                                        />
                                        <div class="p-4">
                                            <h3
                                                class="mb-2 text-lg font-semibold"
                                            >
                                                {{ service.name }}
                                            </h3>
                                            <div
                                                class="flex items-center justify-between mb-3"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <div>
                                                        <i
                                                            class="text-yellow-500 ri-star-fill"
                                                        ></i>
                                                        <span class="ml-1">
                                                            {{
                                                                service.avg_rate
                                                            }}
                                                        </span>
                                                    </div>
                                                    <span
                                                        class="ml-2 text-sm text-gray-500"
                                                    >
                                                        ({{
                                                            service.total_review_count
                                                        }}
                                                        reviews)
                                                    </span>
                                                </div>
                                                <span
                                                    v-if="
                                                        $page.props.auth.user
                                                            .id ===
                                                        service.user_id
                                                    "
                                                >
                                                    Earned ₱{{
                                                        service.weekly_revenue
                                                    }}
                                                    this week
                                                </span>
                                            </div>
                                            <div class="flex gap-5">
                                                <Link
                                                    v-if="
                                                        service.user_id ===
                                                        $page.props.auth.user.id
                                                    "
                                                    :href="
                                                        route(
                                                            'service-provider.services.edit',
                                                            service.id
                                                        )
                                                    "
                                                    class="inline-flex items-center text-sm text-primary hover:underline"
                                                >
                                                    <i
                                                        class="mr-1 ri-edit-line"
                                                    ></i>
                                                    Edit
                                                </Link>
                                                <ModalLinkDialog
                                                    v-if="
                                                        service.user_id ===
                                                        $page.props.auth.user.id
                                                    "
                                                    :href="
                                                        route(
                                                            'service-provider.services.archive',
                                                            service.id
                                                        )
                                                    "
                                                    class="inline-flex items-center text-sm text-red-500 hover:underline"
                                                >
                                                    <i
                                                        class="mr-1 ri-archive-line"
                                                    ></i>
                                                    Archive
                                                </ModalLinkDialog>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center py-12 text-center"
                                >
                                    <i
                                        class="mb-4 text-4xl text-gray-400 ri-service-line"
                                    ></i>
                                    <h3
                                        class="mb-2 text-lg font-medium text-gray-900"
                                    >
                                        No Services Added Yet
                                    </h3>
                                    <p class="mb-4 text-sm text-gray-500">
                                        Get started by adding your first service
                                    </p>
                                    <Link
                                        v-if="user.id === authUser.id"
                                        :href="
                                            route(
                                                'service-provider.services.create'
                                            )
                                        "
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-md bg-primary hover:bg-primary/90"
                                    >
                                        <i class="mr-2 ri-add-line"></i>
                                        Add New Service
                                    </Link>
                                </div>
                            </TabPanel>

                            <!-- Archived Services Tab -->
                            <TabPanel value="1">
                                <div
                                    v-if="archivedServices.length > 0"
                                    class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
                                >
                                    <div
                                        v-for="service in archivedServices"
                                        :key="service.id"
                                        class="overflow-hidden bg-white rounded-lg shadow"
                                    >
                                        <img
                                            :src="service.service_thumbnail"
                                            :alt="service.name"
                                            class="object-cover w-full h-48"
                                        />
                                        <div class="p-4">
                                            <h3
                                                class="mb-2 text-lg font-semibold"
                                            >
                                                {{ service.name }}
                                            </h3>
                                            <div class="flex items-center mb-4">
                                                <i
                                                    class="text-yellow-500 ri-star-fill"
                                                ></i>
                                                <span class="ml-1">{{
                                                    service.avg_rate
                                                }}</span>
                                                <span
                                                    class="ml-2 text-sm text-gray-500"
                                                    >({{
                                                        service.total_review_count
                                                    }}
                                                    reviews)</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center py-12 text-center"
                                >
                                    <i
                                        class="mb-4 text-4xl text-gray-400 ri-archive-line"
                                    ></i>
                                    <h3
                                        class="mb-2 text-lg font-medium text-gray-900"
                                    >
                                        No Archived Services
                                    </h3>
                                    <p class="mb-4 text-sm text-gray-500">
                                        Archived services will appear here
                                    </p>
                                </div>
                            </TabPanel>

                            <!-- Reviews Tab -->
                            <TabPanel value="2">
                                <div v-if="props.feedbacks.length > 0">
                                    <!-- Filters -->
                                    <div class="flex gap-4 mb-6">
                                        <div class="w-48">
                                            <SelectInput
                                                v-model="reviewFilter.rating"
                                                class="w-full"
                                            >
                                                <option value="">
                                                    All Ratings
                                                </option>
                                                <option
                                                    v-for="rating in ratingOptions"
                                                    :key="rating"
                                                    :value="rating"
                                                >
                                                    {{ rating }} Stars
                                                </option>
                                            </SelectInput>
                                        </div>
                                        <div class="w-64">
                                            <SelectInput
                                                v-model="
                                                    reviewFilter.serviceName
                                                "
                                                class="w-full"
                                            >
                                                <option value="">
                                                    All Services
                                                </option>
                                                <option
                                                    v-for="service in services"
                                                    :key="service.id"
                                                    :value="service.name"
                                                >
                                                    {{ service.name }}
                                                </option>
                                            </SelectInput>
                                        </div>
                                    </div>

                                    <!-- Reviews List -->
                                    <div class="space-y-4">
                                        <div
                                            v-for="review in filteredReviews"
                                            :key="review.id"
                                            class="p-4 rounded-lg bg-gray-50"
                                        >
                                            <div
                                                class="flex items-center justify-between mb-2"
                                            >
                                                <div class="font-medium">
                                                    {{
                                                        review.user.profile
                                                            .full_name
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500"
                                                >
                                                    {{
                                                        moment(
                                                            review.created_at
                                                        ).format("MMM D, YYYY")
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                class="mb-2 text-sm text-gray-600"
                                            >
                                                {{ review.serviceName }}
                                            </div>
                                            <div class="flex items-center mb-3">
                                                <div
                                                    class="flex text-yellow-500"
                                                >
                                                    <i
                                                        v-for="star in review.rate"
                                                        :key="star"
                                                        class="ri-star-fill"
                                                    ></i>
                                                </div>
                                            </div>
                                            <p class="text-gray-700">
                                                {{ review.content }}
                                            </p>
                                            <template
                                                v-if="
                                                    review.attachments &&
                                                    review.attachments.length
                                                "
                                            >
                                                <div class="mt-3 space-y-2">
                                                    <div
                                                        class="text-sm font-medium text-gray-700"
                                                    >
                                                        Attachments:
                                                    </div>
                                                    <div
                                                        class="flex flex-wrap gap-2"
                                                    >
                                                        <a
                                                            v-for="attachment in review.attachments"
                                                            :key="attachment.id"
                                                            :href="`/storage/${attachment.file_path}`"
                                                            target="_blank"
                                                            class="inline-flex items-center px-3 py-1 text-sm border rounded-full hover:bg-gray-50"
                                                        >
                                                            <i
                                                                class="mr-1 ri-attachment-2"
                                                            ></i>
                                                            {{
                                                                attachment.file_name
                                                            }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <div
                                            v-if="filteredReviews.length === 0"
                                            class="my-8"
                                        >
                                            <p
                                                class="text-lg font-semibold text-center"
                                            >
                                                No Reviews Found
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="flex flex-col items-center justify-center py-12 text-center"
                                >
                                    <i
                                        class="mb-4 text-4xl text-gray-400 ri-star-smile-line"
                                    ></i>
                                    <h3
                                        class="mb-2 text-lg font-medium text-gray-900"
                                    >
                                        No Reviews Yet
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Reviews will appear here once customers
                                        leave feedback
                                    </p>
                                </div>
                            </TabPanel>
                        </TabPanels>
                    </Tabs>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
