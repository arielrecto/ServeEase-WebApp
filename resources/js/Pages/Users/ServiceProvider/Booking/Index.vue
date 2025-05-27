<script setup>
import { Link, usePage, useForm, router } from "@inertiajs/vue3";
import { ref, reactive, computed, defineAsyncComponent, watch } from "vue";
import moment from "moment";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import ActionButton from "@/Components/ActionButton.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

const Calendar = defineAsyncComponent(() =>
    import("@/Components/Calendar.vue")
);

const state = reactive({
    tabs: [
        { name: "Recent", value: "0" },
        { name: "Finished", value: "1" },
        { name: "Reviews", value: "2" },
    ],
});

const props = defineProps([
    "availServices",
    "latestBookingsCount",
    "pendingBookingsCount",
    "finishedBookingsCount",
    "ongoingBookingsCount",
    "reviewsCount",
    "personalEvents"
]);

const page = usePage();

const authUser = ref(page.props.auth.user);

const isNotServiceProvider = computed(() => {
    return !authUser.value?.roles?.some(
        (role) => role.name === "service provider"
    );
});

const headers = ["Reference #", "Service", "Customer", "Agreed Price", "Address",  "Status", "Actions"];

const openCalendar = ref(false);

const events = ref([]);

const startService = (id) => {
    router.get(`/service-provider/booking/${id}/confirm?status=in_progress`, {
        onSuccess: () => { },
        onError: (errors) => {
            console.error(errors);
        },
    });
};

watch(openCalendar, () => {
    events.value = [
        ...props.availServices.data.map((item) => ({
            title: ``,
            start: item.start_date,
            end: moment(item.end_date).add(1, "day").format("YYYY-MM-DD"),
        })),
    ];
});
</script>

<template>

    <Head title="My Bookings" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                My Bookings
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <button :class="`btn mb-2 ${!openCalendar ? ' ' : 'btn-error'}`" @click="openCalendar = !openCalendar">
                    <i class="ri-calendar-2-line"></i>
                </button>
                <div class="w-full h-full p-2 mb-5 bg-white shadow-sm sm:rounded-lg" v-if="openCalendar">
                    <Calendar :events="[...events, ...personalEvents]"  :can_create="true" />
                </div>
                <div class="flex flex-col justify-center gap-5 mb-5 md:flex-row">
                    <div class="flex-1">
                        <Link :href="route('service-provider.booking.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg">
                        <div>
                            <span>
                                <i class="ri-book-marked-fill"></i>
                                Bookings this week
                            </span>
                        </div>
                        <div class="text-2xl font-black text-primary">
                            {{ latestBookingsCount }}
                        </div>
                        </Link>
                    </div>
                    <div class="flex-1">
                        <Link :href="route('service-provider.booking.index', {
                            filter: 'pending',
                        })
                            "
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg">
                        <div>
                            <span>
                                <i class="ri-error-warning-fill"></i>
                                Pending Bookings
                            </span>
                        </div>
                        <div class="text-2xl font-black text-primary">
                            {{ pendingBookingsCount }}
                        </div>
                        </Link>
                    </div>
                    <div class="flex-1">
                        <Link :href="route('service-provider.booking.index', {
                            filter: 'in_progress',
                        })
                            "
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg">
                        <div>
                            <span>
                                <i class="ri-run-fill"></i>
                                Ongoing Bookings
                            </span>
                        </div>
                        <div class="text-2xl font-black text-primary">
                            {{ ongoingBookingsCount }}
                        </div>
                        </Link>
                    </div>
                    <div class="flex-1">
                        <Link :href="route('service-provider.booking.index', {
                            filter: 'completed',
                        })
                            "
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg">
                        <div>
                            <span>
                                <i class="ri-thumb-up-fill"></i>
                                Finished Bookings
                            </span>
                        </div>
                        <div class="text-2xl font-black text-primary">
                            {{ finishedBookingsCount }}
                        </div>
                        </Link>
                    </div>
                    <div class="flex-1">
                        <Link :href="route('customer.feedbacks.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg">
                        <div>
                            <span>
                                <i class="ri-star-fill"></i>
                                Reviews Submitted
                            </span>
                        </div>
                        <div class="text-2xl font-black text-primary">
                            {{ reviewsCount }}
                        </div>
                        </Link>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <TableWrapper>
                            <template #header>
                                <TableHeader :headers="headers" />
                            </template>

                            <template #body>
                                <template v-if="availServices.data.length !== 0">
                                    <tr v-for="availService in availServices.data" :key="availService.id">
                                        <td class="font-medium">
                                            <Link v-if="availService.reference_number" :href="route('service-provider.booking.cart.show', {
                                                serviceCartId: availService.cart_id,
                                            })" class="text-primary hover:underline">
                                            {{ availService.reference_number }}
                                            </Link>
                                            <span v-else>---</span>
                                        </td>
                                        <th>{{ availService.name }}</th>
                                        <td>
                                            {{ availService.customer }}
                                        </td>
                                        <td class="font-bold">
                                            ₱{{
                                                availService.total_price.toLocaleString()
                                            }}
                                        </td>
                                        <td class="font-bold truncate">
                                           {{ availService.address }}
                                        </td>
                                        <td>
                                            <StatusBadge :status="availService.status" />
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-x-4">
                                                <ActionButton type="link" actionType="view" :href="route(
                                                    'service-provider.booking.detail',
                                                    availService.id
                                                )
                                                    " />
                                                <ActionButton v-if="
                                                    availService.status ===
                                                    'pending'
                                                " type="modal" actionType="confirm" :href="route(
                                                    'service-provider.booking.confirm',
                                                    {
                                                        availService:
                                                            availService.id,
                                                        _query: {
                                                            status: 'confirmed',
                                                        },
                                                    }
                                                )
                                                    " />
                                                <ActionButton v-if="
                                                    availService.status ===
                                                    'pending'
                                                " type="modal" actionType="reject" :href="route(
                                                    'service-provider.booking.confirm',
                                                    {
                                                        availService:
                                                            availService.id,
                                                        _query: {
                                                            status: 'rejected',
                                                        },
                                                    }
                                                )
                                                    " />
                                                <ActionButton v-if="
                                                    availService.status ===
                                                    'confirmed'
                                                " type="modal" actionType="start service" :href="route(
                                                    'service-provider.booking.confirm',
                                                    {
                                                        availService:
                                                            availService.id,
                                                        _query: {
                                                            status: 'in_progress',
                                                        },
                                                    }
                                                )
                                                    " />
                                                <ActionButton v-if="
                                                    availService.status ===
                                                    'in_progress'
                                                " type="modal" actionType="complete" :href="route(
                                                    'service-provider.booking.confirm',
                                                    {
                                                        availService:
                                                            availService.id,
                                                        _query: {
                                                            status: 'completed',
                                                        },
                                                    }
                                                )
                                                    " />
                                                <ActionButton v-if="
                                                    availService.status ===
                                                    'in_progress'
                                                " type="modal" actionType="cancel" :href="route(
                                                    'service-provider.booking.confirm',
                                                    {
                                                        availService:
                                                            availService.id,
                                                        _query: {
                                                            status: 'cancelled',
                                                        },
                                                    }
                                                )
                                                    " />
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td :colspan="headers.length">
                                            <p class="italic text-center">
                                                No records found.
                                            </p>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </TableWrapper>
                    </div>

                    <PaginationLinks :links="availServices.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
