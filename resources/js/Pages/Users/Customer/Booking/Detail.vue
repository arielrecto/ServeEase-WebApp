<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, reactive, computed } from "vue";
import moment from "moment";

import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

const props = defineProps({
    availService: Object,
    service: Object,
});

const state = reactive({
    tabs: [
        { name: "About", value: "0" },
        { name: "Reviews", value: "1" },
    ],
});

const bookingStatus = computed(() => {
    switch (props.availService.status) {
        case "pending":
            return "Pending";
            break;
        case "working":
            return "Working";
            break;
        case "approved":
            return "Approved";
        case "done":
            return "Done";
            break;
    }
});

const bookingStatusBadgeStyle = computed(() => {
    switch (props.availService.status) {
        case "pending":
            return "bg-yellow-100 text-yellow-800";
            break;
        case "working":
            return "bg-orange-100 text-orange-800";
            break;
        case "approved":
            return "bg-green-100 text-green-800";
        case "done":
            break;
    }
});
</script>

<template>
    <Head :title="service.name" />

    <AuthenticatedLayout>
        <template #header>
            <HeaderBackButton :url="'#'" />
        </template>
        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="h-[35vh] w-full">
                        <img
                            :src="
                                service.thumbnail ??
                                'https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'
                            "
                            alt="Image showing the service"
                            class="object-cover w-full h-full"
                        />
                    </div>

                    <div class="p-8">
                        <Tabs value="0">
                            <TabList>
                                <Tab
                                    v-for="tab in state.tabs"
                                    :value="tab.value"
                                    >{{ tab.name }}</Tab
                                >
                            </TabList>
                            <TabPanels>
                                <TabPanel value="0">
                                    <div
                                        class="flex flex-col gap-y-4 md:grid md:grid-cols-3 gap-x-8"
                                    >
                                        <div
                                            class="order-2 col-span-2 md:order-1"
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
                                                    {{
                                                        Math.floor(
                                                            Math.random() * 5 +
                                                                1
                                                        )
                                                    }}
                                                </span>
                                            </div>

                                            <div class="flex flex-col gap-y-1">
                                                <span class="text-gray-600"
                                                    >Description</span
                                                >
                                                <div
                                                    class="overflow-y-auto max-h-48"
                                                >
                                                    <span class="">{{
                                                        service.description
                                                    }}</span>
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    service.terms_and_conditions
                                                "
                                                class="flex flex-col gap-y-1"
                                            >
                                                <span class="text-gray-600"
                                                    >Terms & conditions</span
                                                >
                                                <div
                                                    class="overflow-y-auto max-h-48"
                                                >
                                                    <span class="">{{
                                                        service.terms_and_conditions
                                                    }}</span>
                                                </div>
                                            </div>
                                            <div
                                                class="flex flex-col mt-20 gap-y-3"
                                            >
                                                <span
                                                    class="text-xl font-semibold"
                                                    >About the provider</span
                                                >
                                                <div
                                                    class="flex flex-col gap-y-1"
                                                >
                                                    <div
                                                        class="flex items-start gap-x-4"
                                                    >
                                                        <div
                                                            class="w-16 bg-gray-600 rounded-full aspect-square"
                                                        ></div>
                                                        <div
                                                            class="flex flex-col space-y-1"
                                                        >
                                                            <span
                                                                class="text-xl"
                                                                >{{
                                                                    service.user
                                                                        .name
                                                                }}</span
                                                            >
                                                            <span
                                                                class="text-sm italic text-gray-600"
                                                                >{{
                                                                    service.user
                                                                        .profile
                                                                        .provider_profile
                                                                        .contact
                                                                }}</span
                                                            >
                                                            <span
                                                                class="text-sm italic text-gray-600"
                                                                >Experience:
                                                                {{
                                                                    service.user
                                                                        .profile
                                                                        .provider_profile
                                                                        .experience
                                                                }}</span
                                                            >
                                                            <Link
                                                                :href="
                                                                    route(
                                                                        'profile.showProviderProfile',
                                                                        service
                                                                            .user
                                                                            .profile
                                                                            .provider_profile
                                                                            .id
                                                                    )
                                                                "
                                                                class="underline text-primary"
                                                                >Go to
                                                                Profile</Link
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="order-1 h-auto p-5 space-y-4 border border-gray-300 rounded-lg md:order-2"
                                        >
                                            <div>
                                                <ModalLinkDialog
                                                    v-if="
                                                        availService.status ===
                                                            'done' &&
                                                        !availService.has_feedback
                                                    "
                                                    :href="
                                                        route(
                                                            'customer.feedbacks.create'
                                                        ) +
                                                        `?id=${availService.id}`
                                                    "
                                                    class="cursor-pointer text-primary"
                                                >
                                                    <i
                                                        class="ri-edit-2-line"
                                                    ></i>
                                                    Write a review
                                                </ModalLinkDialog>
                                            </div>
                                            <div class="flex gap-x-12">
                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        Agreed Price
                                                    </div>
                                                    <div class="font-bold">
                                                        ₱
                                                        <span
                                                            class="text-xl text-primary"
                                                            >{{
                                                                availService.total_price.toLocaleString()
                                                            }}</span
                                                        >
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        Status
                                                    </div>
                                                    <div
                                                        class="px-5 py-1 text-sm font-bold rounded-lg"
                                                        :class="
                                                            bookingStatusBadgeStyle
                                                        "
                                                    >
                                                        {{ bookingStatus }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex gap-x-12">
                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        Start Date
                                                    </div>
                                                    <div class="font-bold">
                                                        {{
                                                            moment(
                                                                availService.start_date
                                                            ).format(
                                                                "MMM DD, YYYY"
                                                            )
                                                        }}
                                                    </div>
                                                </div>

                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        End Date
                                                    </div>
                                                    <div class="font-bold">
                                                        {{
                                                            moment(
                                                                availService.end_date
                                                            ).format(
                                                                "MMM DD, YYYY"
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-gray-600">
                                                    Remarks
                                                </div>
                                                <div>
                                                    {{ availService.remarks }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>

                                <TabPanel value="1">
                                    <FeedbackList :service="service" />
                                </TabPanel>
                            </TabPanels>
                        </Tabs>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
