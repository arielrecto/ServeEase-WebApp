<script setup>
import { ref } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import ActionButton from "@/Components/ActionButton.vue";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    reports: Object,
});

const headers = [
    "Reference #",
    "Reported User",
    "Reported By",
    "Type",
    "Status",
    "Date Reported",
    "Actions",
];

const showConfirmModal = ref(false);
const selectedReport = ref(null);

const getStatusClass = (status) => {
    return (
        {
            pending: "warning",
            resolved: "success",
            rejected: "error",
        }[status] || "info"
    );
};

const openConfirmModal = (report) => {
    selectedReport.value = report;
    showConfirmModal.value = true;
};

const resolveReport = (id) => {
    router.put(route("admin.reports.resolve", id), {
        preserveScroll: true,
        onSuccess: () => {
            showConfirmModal.value = false;
            selectedReport.value = null;
        },
    });
};

console.log(props.reports.value);
</script>

<template>
    <Head title="Complaint Reports" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Complaint Reports
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3">
                    <div class="p-6 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500">
                                Total Reports
                            </h3>
                            <i
                                class="text-xl text-primary ri-file-list-3-line"
                            ></i>
                        </div>
                        <p class="text-2xl font-bold">{{ reports.total }}</p>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500">
                                Pending Reports
                            </h3>
                            <i class="text-xl text-warning ri-time-line"></i>
                        </div>
                        <p class="text-2xl font-bold">
                            {{
                                reports.data.filter(
                                    (r) => r.status === "pending"
                                ).length
                            }}
                        </p>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-500">
                                Resolved Reports
                            </h3>
                            <i
                                class="text-xl text-success ri-check-double-line"
                            ></i>
                        </div>
                        <p class="text-2xl font-bold">
                            {{
                                reports.data.filter(
                                    (r) => r.status === "resolved"
                                ).length
                            }}
                        </p>
                    </div>
                </div>

                <!-- Reports Table -->
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
                                            {{ report.user.name }}
                                        </td>
                                        <td>
                                            {{ report.reported_by.name }}
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
                                                {{
                                                    report.type ===
                                                    "illegal_transaction"
                                                        ? "Illegal Transaction"
                                                        : "Unfinished Booking"
                                                }}
                                            </span>
                                        </td>
                                        <td>
                                            <StatusBadge
                                                :status="report.status"
                                            />
                                        </td>
                                        <td>
                                            {{
                                                new Date(
                                                    report.created_at
                                                ).toLocaleDateString()
                                            }}
                                        </td>
                                        <td>
                                            <div
                                                class="flex items-center gap-x-2"
                                            >
                                                <ActionButton
                                                    type="link"
                                                    actionType="view"
                                                    :href="
                                                        route(
                                                            'admin.reports.show',
                                                            report.id
                                                        )
                                                    "
                                                />
                                                <ActionButton
                                                    v-if="
                                                        report.status !==
                                                        'resolved'
                                                    "
                                                    type="button"
                                                    actionType="resolve"
                                                    @button-click="
                                                        openConfirmModal(report)
                                                    "
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td
                                            :colspan="headers.length"
                                            class="italic text-center"
                                        >
                                            No reports found.
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </TableWrapper>

                        <!-- Confirmation Modal -->
                        <Modal
                            :show="showConfirmModal"
                            @close="showConfirmModal = false"
                            maxWidth="sm"
                        >
                            <div class="p-6">
                                <div class="">
                                    <!-- <i
                                        class="text-4xl text-primary ri-check-double-line"
                                    ></i> -->
                                    <h3
                                        class="mt-4 text-lg font-medium text-gray-900"
                                    >
                                        Resolve Report
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-600">
                                        Are you sure you want to resolve this
                                        report? This action cannot be undone.
                                    </p>
                                </div>

                                <div
                                    class="flex justify-end gap-3 mt-6 !uppercase"
                                >
                                    <button
                                        type="button"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 uppercase bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                        @click="showConfirmModal = false"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="button"
                                        class="px-4 py-2 text-sm font-medium text-white uppercase rounded-md bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                        @click="
                                            resolveReport(selectedReport.id)
                                        "
                                    >
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </Modal>

                        <PaginationLinks :links="reports.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
