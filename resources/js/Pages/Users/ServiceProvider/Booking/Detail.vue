<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, reactive, computed, watch } from "vue";
import moment from "moment";

import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import FileView from "@/Components/FileView.vue";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

const props = defineProps({
    availService: Object,
    service: Object,
});

// Add computed property for service cart reference
const cartReference = computed(() => {
    return props.availService.service_cart_id
        ? {
              reference: props.availService.service_cart?.reference_number,
              id: props.availService.service_cart_id,
          }
        : null;
});

const state = reactive({
    tabs: [
        { name: "About", value: "0" },
        { name: "Reviews", value: "1" },
    ],
});

const bookingStatus = computed(() => {
    return {
        cancelled: "Cancelled",
        pending: "Pending",
        in_progress: "In Progress",
        confirmed: "Confirmed",
        completed: "Completed",
    }[props.availService.status];
});

const bookingStatusBadgeStyle = (status) => {
    switch (status) {
        case "cancelled":
            return "bg-red-100 text-red-800";
            break;
        case "pending":
            return "bg-yellow-100 text-yellow-800";
            break;
        case "in_progress":
            return "bg-orange-100 text-orange-800";
            break;
        case "confirmed":
            return "bg-green-100 text-green-800";
        case "completed":
            break;
    }
};

const statusOptions = [
    "Cancelled",
    "Pending",
    "In Progress",
    "Confirmed",
    "Completed",
];

const options = [
    { label: "Cancelled", value: "cancelled" },
    { label: "Pending", value: "pending" },
    { label: "In Progress", value: "in_progress" },
    { label: "Confirmed", value: "confirmed" },
    { label: "Completed", value: "completed" },
];

const isOpenUpdateStatusForm = ref(false);

const selectedStatus = ref(bookingStatus.value);

const back = () => {
    window.history.back();
};

watch(selectedStatus, (newStatus) => {
    if (newStatus !== bookingStatus.value) {
        console.log(newStatus, "Selected status changed");
        const form = useForm({
            status: newStatus.toLocaleLowerCase(),
        });
        form.put(
            `/service-provider/booking/${props.availService?.id}/update/status`,
            {
                onSuccess: () => {
                    bookingStatus.value = newStatus;
                    console.log("Status updated successfully");
                    isOpenUpdateStatusForm.value = false;
                },
                onError: (errors) => {
                    console.error(errors);
                },
            }
        );
    }
});
</script>

<template>
    <Head :title="service.name" />

    <AuthenticatedLayout>
        <template #header>
            <button @click="back" type="button" class="button-ghost">
                <i class="fi fi-br-arrow-left"></i>
            </button>
            <!-- <HeaderBackButton :url="'#'" /> -->
        </template>
        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="h-[35vh] w-full">
                        <img
                            :src="service.service_thumbnail"
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
                                            <!-- Add reference number section -->
                                            <div
                                                v-if="cartReference"
                                                class="mb-4 space-y-1"
                                            >
                                                <div class="text-gray-600">
                                                    Reference Number
                                                </div>
                                                <div>
                                                    <Link
                                                        :href="
                                                            route(
                                                                'service-provider.booking.cart.show',
                                                                cartReference.id
                                                            )
                                                        "
                                                        class="font-semibold text-primary hover:underline"
                                                    >
                                                        {{
                                                            cartReference.reference
                                                        }}
                                                    </Link>
                                                </div>
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

                                                    <!-- <SelectInput
                                                        v-show="
                                                            isOpenUpdateStatusForm
                                                        "
                                                        id="sex"
                                                        class="block w-full mt-1"
                                                        v-model="selectedStatus"
                                                        required
                                                    >
                                                        <option
                                                            v-for="option in options"
                                                            :key="option.value"
                                                            :value="
                                                                option.value
                                                            "
                                                        >
                                                            {{ option.label }}
                                                        </option>
                                                    </SelectInput> -->

                                                    <!-- <select
                                                        v-show="
                                                            isOpenUpdateStatusForm
                                                        "
                                                        v-model="selectedStatus"
                                                        id="status"
                                                        class="block w-full p-2 mt-2 border border-gray-300 rounded-md"
                                                    >
                                                        <option
                                                            v-for="status in statusOptions"
                                                            :key="status"
                                                            :value="status"
                                                        >
                                                            {{ status }}
                                                        </option>
                                                    </select> -->
                                                </div>
                                            </div>
                                            <div
                                                v-if="availService.start_time"
                                                class="space-y-1"
                                            >
                                                <div class="text-gray-600">
                                                    Time
                                                </div>
                                                <div>
                                                    {{
                                                        moment(
                                                            availService.start_time,
                                                            "HH:mm"
                                                        ).format("hh:mm A")
                                                    }}
                                                    -
                                                    {{
                                                        moment(
                                                            availService.end_time,
                                                            "HH:mm"
                                                        ).format("hh:mm A")
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                v-if="
                                                    availService.remarks
                                                        .length > 0 &&
                                                    availService.status ===
                                                        'cancelled'
                                                "
                                                class="space-y-1"
                                            >
                                                <div class="text-gray-600">
                                                    Cancellation Reason
                                                </div>
                                                <div class="font-bold">
                                                    {{
                                                        availService.remarks[0]
                                                            .content
                                                    }}
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
                                                        >
                                                            {{
                                                                availService.total_hours.toLocaleString()
                                                            }}
                                                        </span>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="text-gray-600">
                                                        Total Work Days
                                                    </div>
                                                    <div class="font-bold">
                                                        <span
                                                            class="text-xl text-primary"
                                                        >
                                                            {{
                                                                moment(
                                                                    availService.end_date
                                                                ).diff(
                                                                    moment(
                                                                        availService.start_date
                                                                    ),
                                                                    "days"
                                                                )
                                                            }}
                                                        </span>
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
                                                    >
                                                        {{
                                                            availService.service.price.toLocaleString()
                                                        }}
                                                        *
                                                    </span>

                                                    <span
                                                        class="text-xl text-primary"
                                                    >
                                                        {{
                                                            availService.service.price_type.toLocaleString()
                                                        }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Add this after the Service Price * rate section -->
                                            <div class="space-y-1">
                                                <div class="text-gray-600">
                                                    Quantity
                                                </div>
                                                <div class="font-bold">
                                                    <span
                                                        class="text-xl text-primary"
                                                    >
                                                        {{
                                                            availService.quantity
                                                        }}
                                                    </span>
                                                    <span
                                                        class="ml-1 text-sm text-gray-500"
                                                    >
                                                        {{
                                                            availService.quantity >
                                                            1
                                                                ? "units"
                                                                : "unit"
                                                        }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Add this if quantity is more than 1 -->
                                            <div
                                                class="space-y-1"
                                                v-if="availService.quantity > 1"
                                            >
                                                <div class="text-gray-600">
                                                    Price per unit
                                                </div>
                                                <div class="font-bold">
                                                    ₱<span
                                                        class="text-xl text-primary"
                                                    >
                                                        {{
                                                            (
                                                                availService.total_price /
                                                                availService.quantity
                                                            ).toLocaleString()
                                                        }}
                                                    </span>
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
                                                <div
                                                    v-if="
                                                        availService.remarks
                                                            .length > 0
                                                    "
                                                >
                                                    {{
                                                        availService
                                                            .remarks?.[0]
                                                            ?.content
                                                    }}
                                                </div>
                                                <p v-else>No remarks.</p>
                                            </div>
                                            <div
                                                class="space-y-1"
                                                v-if="
                                                    availService.attachments
                                                        ?.length
                                                "
                                            >
                                                <div
                                                    class="p-4 border rounded-lg bg-gray-50"
                                                >
                                                    <h3
                                                        class="flex items-center mb-4 text-lg font-medium"
                                                    >
                                                        <i
                                                            class="mr-2 fas fa-paperclip text-primary"
                                                        ></i>
                                                        Attachments
                                                    </h3>

                                                    <div
                                                        class="grid grid-cols-1 gap-3"
                                                    >
                                                        <FileView
                                                            v-for="file in availService.attachments"
                                                            :key="file.id"
                                                            :file="file"
                                                            :show-delete="false"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <ModalLinkDialog
                                                    v-if="
                                                        !availService.is_fully_paid
                                                    "
                                                    :href="
                                                        route(
                                                            'service-provider.booking.confirm',
                                                            {
                                                                availService:
                                                                    availService.id,
                                                                _query: {
                                                                    status: 'fully_paid',
                                                                },
                                                            }
                                                        )
                                                    "
                                                    class="flex items-center justify-center w-full gap-3 px-4 py-2 mt-4 text-sm font-medium text-white uppercase rounded-lg bg-primary hover:bg-primary/90"
                                                >
                                                    <i
                                                        class="ri-check-double-line"
                                                    ></i>
                                                    <span
                                                        >Mark as Fully
                                                        Paid</span
                                                    >
                                                </ModalLinkDialog>
                                                <!-- <Link
                                                //     v-if="!availService.is_fully_paid"
                                                //     :href="
                                                //         route(
                                                //             'customer.booking.payment',
                                                //             availService.id
                                                //         )
                                                //     "
                                                //     class="flex items-center justify-center w-full gap-2 py-2 text-sm text-white uppercase rounded-lg bg-primary hover:bg-primary/90"
                                                // >
                                                //     <i
                                                //         class="ri-check-double-line"
                                                //     ></i>
                                                //     Mark as Fully Paid
                                                // </Link> -->
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
