<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, reactive, computed, getCurrentInstance } from "vue";
import moment from "moment";

import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

const props = defineProps({
    availService: Object,
    service: Object,
});

const instance = getCurrentInstance();
const state = reactive({
    tabs: [
        { name: "About", value: "0" },
        { name: "Reviews", value: "1" },
    ],
});

const back = () => {
    window.history.back();
};
</script>

<template>
    <Head :title="service.name" />

    <AuthenticatedLayout>
        <template #header>
            <button @click="back" type="button" class="button-ghost">
                <i class="fi fi-br-arrow-left"></i>
            </button>
        </template>
        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="h-[35vh] w-full">
                        <img
                            :src="
                                service.service_thumbnail ??
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
                                    :key="tab.value"
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
                                                    {{ service.avg_rate }}
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
                                                            class="w-16 h-16 overflow-hidden bg-gray-600 rounded-full aspect-square"
                                                        >
                                                            <img
                                                                :src="
                                                                    service.user
                                                                        .profile
                                                                        .user_avatar
                                                                "
                                                                class="object-cover w-full h-full"
                                                            />
                                                        </div>
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
                                                            'completed' &&
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
                                                    <StatusBadge
                                                        :status="
                                                            availService.status
                                                        "
                                                    />
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <template
                                                    v-if="
                                                        availService.service
                                                            .price_type === 'hr'
                                                    "
                                                >
                                                    <div class="text-gray-600">
                                                        Total Work Hours
                                                    </div>
                                                    <div class="font-bold">
                                                        <span
                                                            class="text-xl text-primary"
                                                            >{{
                                                                availService.total_hours.toLocaleString()
                                                            }}</span
                                                        >
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="text-gray-600">
                                                        Total Work Days
                                                    </div>
                                                    <div class="font-bold">
                                                        <span
                                                            class="text-xl text-primary"
                                                            >{{
                                                                moment(
                                                                    availService.end_date
                                                                ).diff(
                                                                    moment(
                                                                        availService.start_date
                                                                    ),
                                                                    "days"
                                                                )
                                                            }}</span
                                                        >
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="space-y-1">
                                                <div class="text-gray-600">
                                                    Service Price * rate
                                                </div>
                                                <div class="font-bold">
                                                    ₱<span
                                                        class="text-xl text-primary"
                                                        >{{
                                                            availService.service.price.toLocaleString()
                                                        }}
                                                        *
                                                    </span>

                                                    <span
                                                        class="text-xl text-primary"
                                                        >{{
                                                            availService.service.price_type.toLocaleString()
                                                        }}</span
                                                    >
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

                                            <Link
                                                :href="
                                                    route('messages.index', {
                                                        participant_id:
                                                            service.user.id,
                                                    })
                                                "
                                                class="flex items-center justify-center w-full gap-2 py-2 text-white rounded-lg bg-primary hover:bg-primary-dark"
                                            >
                                                <i
                                                    class="ri-message-3-line"
                                                ></i>
                                                Message Provider
                                            </Link>
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
