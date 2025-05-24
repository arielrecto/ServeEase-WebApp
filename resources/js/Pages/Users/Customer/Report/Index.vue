<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TableWrapper from '@/Components/Table/TableWrapper.vue';
import TableHeader from '@/Components/Table/TableHeader.vue';
import PaginationLinks from '@/Components/PaginationLinks.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    reports: Object,
});

const headers = [
    'Reference #',
    'Reported User',
    'Complaint',
    'Type',
    'Status',
    'Date',
    'Actions'
];

const getTypeLabel = (type) => {
    return {
        'illegal_transaction': 'Illegal Transaction',
        'unfinished_booking': 'Unfinished Booking'
    }[type] || type;
};
</script>

<template>
    <Head title="My Reports" />

    <AuthenticatedLayout>
        <template #header>
            <div class="w-full flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Reports
                </h2>

                <Link
                    :href="route('customer.report.create')"
                    class="px-4 py-2 text-sm font-semibold  bg-primary rounded-lg text-white hover:bg-primary-dark "
                >
                    File a Complaint
                </Link>
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
                                    <tr v-for="report in reports.data" :key="report.id">
                                        <td class="font-medium">
                                            #{{ report.id }}
                                        </td>
                                        <td>
                                            {{ report.user.name }}
                                        </td>
                                        <td class="max-w-md">
                                            <p class="truncate">
                                                {{ report.complaint }}
                                            </p>
                                        </td>
                                        <td>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                                :class="{
                                                    'bg-red-100 text-red-700': report.type === 'illegal_transaction',
                                                    'bg-yellow-100 text-yellow-700': report.type === 'unfinished_booking'
                                                }">
                                                {{ getTypeLabel(report.type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <StatusBadge :status="report.status" />
                                        </td>
                                        <td>
                                            {{ report.created_at }}
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-x-2">
                                                <Link
                                                    :href="route('customer.report.show', report.id)"
                                                    class="text-sm text-primary hover:text-primary-dark"
                                                >
                                                    View Details
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td :colspan="headers.length" class="py-8 text-center text-gray-500">
                                            No reports found.
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </TableWrapper>

                        <PaginationLinks :links="reports.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
