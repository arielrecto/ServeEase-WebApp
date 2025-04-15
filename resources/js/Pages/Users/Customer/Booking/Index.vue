<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed } from "vue";

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
    "reviewsCount",
]);

const page = usePage();

const authUser = ref(page.props.auth.user);

const isNotServiceProvider = computed(() => {
    return !authUser.value?.roles?.some(
        (role) => role.name === "service provider"
    );
});

const headers = ["Service", "Provider", "Agreed Price", "Status", "Actions"];

console.log(props.availServices);
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
                <div
                    class="flex flex-col justify-center gap-5 mb-5 md:flex-row"
                >
                    <div class="flex-1">
                        <Link
                            :href="route('customer.booking.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
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
                        <Link
                            :href="
                                route('customer.booking.index', {
                                    filter: 'pending',
                                })
                            "
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
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
                        <Link
                            :href="
                                route('customer.booking.archive', {
                                    filter: 'completed',
                                })
                            "
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
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
                        <Link
                            :href="route('customer.feedbacks.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
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
                                <template
                                    v-if="availServices.data.length !== 0"
                                >
                                    <tr
                                        v-for="availService in availServices.data"
                                    >
                                        <th>{{ availService.name }}</th>
                                        <td>
                                            {{ availService.provider }}
                                        </td>
                                        <td class="font-bold">
                                            ₱{{
                                                availService.total_price.toLocaleString()
                                            }}
                                        </td>
                                        <td>
                                            <StatusBadge
                                                :status="availService.status"
                                            />
                                        </td>
                                        <td>
                                            <div
                                                class="flex items-center gap-x-4"
                                            >
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
                                                        `?id=${availService.service_id}`
                                                    "
                                                    class="cursor-pointer text-primary"
                                                >
                                                    <i
                                                        class="ri-edit-2-line"
                                                    ></i>
                                                    Write a review
                                                </ModalLinkDialog>

                                                <ActionButton
                                                    type="link"
                                                    actionType="view"
                                                    :href="
                                                        route(
                                                            'customer.booking.detail',
                                                            availService.id
                                                        )
                                                    "
                                                />
                                                <ActionButton
                                                    type="link"
                                                    actionType="edit"
                                                    :href="'#'"
                                                />
                                                <ActionButton
                                                    v-if="availService.service_cart_id"
                                                    type="link"
                                                    actionType="cart"
                                                    :href="route('customer.booking.cart.show', availService.service_cart_id)"
                                                />
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
