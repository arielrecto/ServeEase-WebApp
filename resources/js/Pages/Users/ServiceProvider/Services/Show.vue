<script setup>
import { Link, usePage } from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import ActionButton from "@/Components/ActionButton.vue";
import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

const props = defineProps({
    service: Object,
    availServices: Object,
    chartData: Object,
});

const headers = [
    "Service",
    "Provider",
    "Customer",
    "Agreed Price",
    "Status",
    "Actions",
];

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
} from "chart.js";

import { Bar, Pie } from "vue-chartjs";

const options = {
    responsive: true,
    maintainAspectRatio: false,
};

const data = props.chartData.monthlySales;
const pieData = props.chartData.ratings;

const pieOptions = {
    responsive: true,
    maintainAspectRatio: false,
};

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
    ArcElement
);
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-col gap-y-5">
                    <div class="overflow-hidden aspect-video">
                        <img
                            :src="
                                service.thumbnail ??
                                'https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'
                            "
                            alt="Service thumbnail"
                            class="object-cover"
                        />
                    </div>
                    <div class="flex flex-col gap-y-1">
                        <span class="font-semibold">Description</span>
                        <div class="overflow-y-auto max-h-48">
                            <span class="text-gray-600">{{
                                service.description
                            }}</span>
                        </div>
                    </div>
                    <div
                        v-if="service.terms_and_conditions"
                        class="flex flex-col gap-y-1"
                    >
                        <span class="font-semibold">Description</span>
                        <div class="overflow-y-auto max-h-48">
                            <span class="text-gray-600">{{
                                service.terms_and_conditions
                            }}</span>
                        </div>
                    </div>
                    <div class="w-full h-96">
                        <Bar :data="data" :options="options" />
                    </div>
                    <div class="w-full h-96">
                        <Pie :data="pieData" :options="pieOptions" />
                    </div>

                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                    >
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
                                            :key="availService.id"
                                        >
                                            <th>
                                                {{ availService.service.name }}
                                            </th>
                                            <td>
                                                {{
                                                    availService.service.user
                                                        .name
                                                }}
                                            </td>
                                            <td>
                                                {{ availService.user.name }}
                                            </td>
                                            <td class="font-bold">
                                                ₱{{
                                                    availService.total_price.toLocaleString()
                                                }}
                                            </td>
                                            <td>
                                                <StatusBadge
                                                    :status="
                                                        availService.status
                                                    "
                                                />
                                            </td>
                                            <td>
                                                <div
                                                    class="flex items-center gap-x-4"
                                                >
                                                    <!-- <ModalLinkDialog
                                                        v-if="
                                                            availService.status ===
                                                                'done' &&
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
                                                    </ModalLinkDialog> -->

                                                    <ActionButton
                                                        type="link"
                                                        actionType="view"
                                                        :href="
                                                            route(
                                                                'service-provider.booking.detail',
                                                                availService.id
                                                            )
                                                        "
                                                    />
                                                    <!-- <ActionButton
                                                        type="link"
                                                        actionType="edit"
                                                        :href="'#'"
                                                    /> -->
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

                    <!-- <div class="flex flex-col mt-20 gap-y-3">
                        <span class="text-xl font-semibold"
                            >About the provider</span
                        >
                        <div class="flex flex-col gap-y-1">
                            <div class="flex items-start gap-x-4">
                                <div
                                    class="w-16 bg-gray-600 rounded-full aspect-square"
                                ></div>
                                <div class="flex flex-col space-y-1">
                                    <span class="text-xl">{{
                                        service.user.name
                                    }}</span>
                                    <span
                                        class="text-sm italic text-gray-600"
                                        >{{
                                            service.user.profile
                                                .provider_profile.contact
                                        }}</span
                                    >
                                    <span class="text-sm italic text-gray-600"
                                        >Experience:
                                        {{
                                            service.user.profile
                                                .provider_profile.experience
                                        }}</span
                                    >

                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
