<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    reports: {
        type: Object,
        required: true,
    },
});

const headers = ["Reference #", "Type", "Status", "Date Reported"];

const getTypeLabel = (type) => {
    return (
        {
            illegal_transaction: "Illegal Transaction",
            unfinished_booking: "Unfinished Booking",
        }[type] || type
    );
};
</script>

<template>
    <Head title="Received Complaints" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route(`customer.report.index`)" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Received Complaints
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <TableWrapper>
                            <template #header>
                                <TableHeader :headers="headers" />
                            </template>

                            <template #body>
                                <template v-if="reports.data.length">
                                    <tr
                                        v-for="report in reports.data"
                                        :key="report.id"
                                    >
                                        <td class="font-medium">
                                            #{{ report.id }}
                                        </td>
                                        <td>
                                            <span
                                                class="px-2 py-1 text-xs font-medium rounded-full"
                                                :class="{
                                                    'bg-red-100 text-red-700':
                                                        report.type ===
                                                        'illegal_transaction',
                                                    'bg-yellow-100 text-yellow-700':
                                                        report.type ===
                                                        'unfinished_booking',
                                                }"
                                            >
                                                {{ getTypeLabel(report.type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <StatusBadge
                                                :status="report.status"
                                            />
                                        </td>
                                        <td class="text-gray-500">
                                            {{ report.created_at }}
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td
                                            :colspan="headers.length"
                                            class="py-8 italic text-center text-gray-500"
                                        >
                                            No complaints received.
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </TableWrapper>

                        <div class="mt-6">
                            <PaginationLinks :links="reports.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
