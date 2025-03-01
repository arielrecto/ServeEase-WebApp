<script setup>
import { reactive } from "vue";
import moment from "moment";
import { Head, usePage, Link } from "@inertiajs/vue3";
import axios from "axios";

import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

const page = usePage();

const authUser = page.props.auth.user;

const props = defineProps([
    "providerProfile",
    "feedbackCount",
    "finishedBookingsCount",
    "bookingsCount",
    "user",
    "profile",
    "service",
]);

const state = reactive({
    tabs: [
        { name: "Service", value: "0" },
        { name: "Reviews", value: "1" },
    ],
});

const ratingOptions = [5, 4, 3, 2, 1];
</script>

<template>
    <Head title="Provider Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton
                    :url="route('admin.service-provider.index')"
                />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ user.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div
                        class="flex flex-col items-start justify-between gap-5 md:flex-row"
                    >
                        <div class="flex items-center gap-x-6">
                            <div
                                class="w-16 h-16 bg-gray-300 rounded-full q md:w-28 md:h-28"
                            ></div>
                            <div class="space-y-1">
                                <div class="text-xl">{{ user.name }}</div>
                                <div class="text-sm italic text-gray-600">
                                    {{ providerProfile.contact }}
                                </div>
                                <div class="text-sm italic text-gray-600">
                                    Experience: {{ providerProfile.experience }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-x-4">
                            <div
                                class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72"
                            >
                                <div>
                                    <span> Finished Transactions </span>
                                </div>
                                <div class="text-2xl font-black text-primary">
                                    {{ finishedBookingsCount }}
                                </div>
                            </div>

                            <div
                                class="w-full h-auto p-6 border border-gray-300 rounded-lg md:w-72"
                            >
                                <div>
                                    <span> Total transactions </span>
                                </div>
                                <div class="text-2xl font-black text-primary">
                                    {{ bookingsCount }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <Tabs value="0">
                        <TabList>
                            <Tab v-for="tab in state.tabs" :value="tab.value">{{
                                tab.name
                            }}</Tab>
                        </TabList>
                        <TabPanels>
                            <TabPanel value="0">
                                <div
                                    v-if="service"
                                    class="flex flex-col gap-y-4 md:grid md:grid-cols-3 gap-x-8"
                                >
                                    <div
                                        class="order-2 col-span-2 space-y-6 md:order-1"
                                    >
                                        <div class="mb-12 space-y-2">
                                            <h1 class="text-2xl font-bold">
                                                {{ service.name }}
                                            </h1>
                                            <span
                                                class="inline-block mr-3 text-sm"
                                            >
                                                <i
                                                    class="text-yellow-500 fa-solid fa-star"
                                                ></i>
                                                {{ service.avg_rate }} ({{
                                                    feedbackCount
                                                }})
                                            </span>
                                        </div>

                                        <div
                                            class="overflow-hidden aspect-video"
                                        >
                                            <img
                                                :src="service.service_thumbnail"
                                                alt="Service thumbnail"
                                                class="object-cover"
                                            />
                                        </div>

                                        <div v-if="authUser.id === user.id">
                                            <Link href="#" class="text-primary"
                                                ><i class="ri-edit-2-line"></i>
                                                Edit details</Link
                                            >
                                        </div>

                                        <div class="flex flex-col gap-y-1">
                                            <span class="text-gray-600"
                                                >Description</span
                                            >
                                            <div
                                                class="overflow-y-auto max-h-48"
                                            >
                                                <span class="text-black">{{
                                                    service.description
                                                }}</span>
                                            </div>
                                        </div>
                                        <div
                                            v-if="service.terms_and_conditions"
                                            class="flex flex-col gap-y-1"
                                        >
                                            <span class="text-gray-600"
                                                >Terms & conditions</span
                                            >
                                            <div
                                                class="overflow-y-auto max-h-48"
                                            >
                                                <span class="text-black">{{
                                                    service.terms_and_conditions
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="h-[20vh] flex flex-col gap-y-4 items-center justify-center"
                                >
                                    <div class="text-gray-600">
                                        No service found.
                                    </div>
                                </div>
                            </TabPanel>

                            <TabPanel value="1">
                                <div
                                    v-if="service"
                                    class="flex flex-col mb-6 space-y-1"
                                >
                                    <div
                                        class="inline-block mr-3 text-2xl font-bold"
                                    >
                                        <i
                                            class="text-yellow-500 fa-solid fa-star"
                                        ></i>
                                        {{ service?.avg_rate ?? 0 }} / 5
                                    </div>
                                    <div class="text-gray-500">
                                        {{ feedbackCount }} total reviews
                                    </div>
                                </div>
                                <FeedbackList
                                    v-if="service"
                                    :service="service"
                                />
                                <div
                                    v-else
                                    class="h-[20vh] flex flex-col gap-y-4 items-center justify-center"
                                >
                                    <div class="text-gray-600">
                                        No service found.
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
