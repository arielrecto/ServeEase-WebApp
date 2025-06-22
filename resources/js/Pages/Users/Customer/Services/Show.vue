<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import moment from "moment";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AddToFavorites from "@/Components/AddToFavorites.vue";
import Calendar from "@/Components/Calendar.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";

const props = defineProps({
    service: Object,
    availServices: Array,
    totalUserServices: Number,
    ongoingBookingsCount: Number,
    personalEvents: Array,
});

const events = computed(() => {
    if (props.availServices.length === 0) return [];
    console.log(props.availServices);
    return [
        ...props.availServices.map((item) => ({
            title: `Booking with ${item.user.profile.full_name}`,
            start: item.start_time
                ? `${item.start_date}T${item.start_time}`
                : item.start_date,
            end: item.end_time
                ? `${item.end_date}T${item.end_time}`
                : moment(item.end_date).add(1, "day").format("YYYY-MM-DD"),
            allDay: !item.start_time || !item.end_time,
            extendedProps: {
                time:
                    item.start_time && item.end_time
                        ? `${moment(item.start_time, "HH:mm").format(
                              "h:mm A"
                          )} - ${moment(item.end_time, "HH:mm").format(
                              "h:mm A"
                          )}`
                        : "All Day",
                status: item.status,
            },
        })),
    ];
});

const showEventDetails = ref(false);
const selectedEvent = ref(null);

const handleEventClick = (event) => {
    selectedEvent.value = event;
    showEventDetails.value = true;
};
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    View Service
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div
                class="grid grid-cols-1 gap-5 mx-auto max-w-7xl sm:px-6 lg:px-8 md:grid-cols-3"
            >
                <div class="flex flex-col col-span-2 gap-5">
                    <div class="bg-white rounded-lg shadow-lg">
                        <!-- Service Header -->
                        <div class="p-6 border-b">
                            <div class="flex items-center justify-between mb-4">
                                <h1 class="text-3xl font-bold text-primary">
                                    {{ service.name }}
                                </h1>
                                <!-- <AddToFavorites :service="service" /> -->
                            </div>
                            <div
                                class="flex items-center justify-between text-gray-600"
                            >
                                <div class="flex items-center gap-2">
                                    <i class="ri-user-line"></i>
                                    <span>{{
                                        service.user.profile.full_name
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 font-semibold text-primary"
                                >
                                    <i class="ri-money-dollar-circle-line"></i>
                                    <span
                                        >₱{{ service.price }} /
                                        {{ service.price_type }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Service Image -->
                        <div class="px-6 py-8 border-b">
                            <img
                                :src="service.service_thumbnail"
                                :alt="service.name"
                                class="object-cover w-full max-w-2xl mx-auto rounded-lg shadow-md"
                            />
                        </div>

                        <!-- Service Details -->
                        <div class="p-6 space-y-6">
                            <div class="space-y-3">
                                <h2 class="text-xl font-semibold">
                                    Description
                                </h2>
                                <p
                                    class="p-4 text-gray-700 rounded-lg bg-gray-50"
                                >
                                    {{ service.description }}
                                </p>
                            </div>

                            <div class="space-y-3">
                                <h2 class="text-xl font-semibold">
                                    Terms & Conditions
                                </h2>
                                <p
                                    class="p-4 text-gray-700 rounded-lg bg-gray-50"
                                >
                                    {{ service.terms_and_conditions }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            class="flex justify-end gap-3 p-6 rounded-b-lg bg-gray-50"
                        >
                            <Link
                                v-if="totalUserServices > 1"
                                :href="
                                    route('customer.services.bulk-form', {
                                        provider_id: service.user.id,
                                        query: { service_id: service.id },
                                    })
                                "
                                class="btn btn-secondary"
                            >
                                <i class="mr-2 ri-add-line"></i>
                                Add to Bulk Service
                            </Link>
                            <Link
                                :href="
                                    route(
                                        'customer.services.avail.create',
                                        service.id
                                    )
                                "
                                class="btn btn-primary"
                            >
                                <i class="mr-2 ri-shopping-cart-line"></i>
                                Avail Now
                            </Link>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="p-5 bg-white rounded-lg shadow-lg h-max">
                        <h2 class="mb-4">
                            {{ service.user.profile.first_name }}'s upcoming
                            bookings & events
                        </h2>
                        <Calendar
                            class="w-auto h-auto"
                            :events="[...events, ...personalEvents]"
                            :onEventClick="handleEventClick"
                        />
                    </div>

                    <div class="flex-1">
                        <div
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-lg"
                        >
                            <div>
                                <span>
                                    <i class="ri-run-fill"></i>
                                    {{ service.user.profile.first_name }}'s
                                    Ongoing Bookings
                                </span>
                            </div>
                            <div class="text-2xl font-black text-primary">
                                {{ ongoingBookingsCount }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col p-6 bg-white rounded-lg shadow-lg gap-y-1"
                    >
                        <div class="flex items-start gap-x-4">
                            <div
                                class="w-16 h-16 overflow-hidden bg-gray-600 rounded-full aspect-square"
                            >
                                <img
                                    :src="service.user.profile.user_avatar"
                                    class="object-cover w-full h-full"
                                />
                            </div>
                            <div class="flex flex-col space-y-1">
                                <span class="text-xl">{{
                                    service.user.profile.full_name
                                }}</span>
                                <span class="text-sm italic text-gray-600">{{
                                    service.user.profile.provider_profile
                                        .contact
                                }}</span>
                                <span class="text-sm italic text-gray-600"
                                    >Address:
                                    {{
                                        service.user.profile.provider_profile
                                            .address
                                    }}</span
                                >
                                <span class="text-sm italic text-gray-600">
                                    Experience:
                                    {{
                                        service.user.profile.provider_profile
                                            .experience
                                    }}
                                    {{
                                        service.user.profile.provider_profile
                                            .experience_duration
                                    }}
                                </span>
                                <div class="flex items-center gap-x-2">
                                    <Link
                                        :href="
                                            route(
                                                'profile.showProviderProfile',
                                                service.user.profile
                                                    .provider_profile.id
                                            )
                                        "
                                        class="underline text-primary"
                                    >
                                        Go to Profile
                                    </Link>
                                    <Link
                                        :href="
                                            route('messages.index', {
                                                participant_id: service.user.id,
                                            })
                                        "
                                        class="underline text-primary"
                                    >
                                        Message Provider
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Add Modal for Event Details -->
    <div v-if="showEventDetails" class="fixed inset-0 z-50 overflow-y-auto">
        <div
            class="fixed inset-0 transition-opacity bg-black bg-opacity-50"
            @click="showEventDetails = false"
        ></div>

        <div class="flex items-center justify-center min-h-full p-4">
            <div
                class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium">Event Details</h3>
                    <button
                        @click="showEventDetails = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4" v-if="selectedEvent">
                    <div>
                        <h4 class="font-medium">{{ selectedEvent.title }}</h4>
                        <p class="text-sm text-gray-600">
                            {{
                                moment(selectedEvent.start).format(
                                    "MMMM D, YYYY"
                                )
                            }}
                            <span v-if="selectedEvent.extendedProps.time">
                                at {{ selectedEvent.extendedProps.time }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- <template>
    <h1>
        Customer dashboard
    </h1>


    <Link href="/logout" method="POST">Log out</Link>
</template> -->
