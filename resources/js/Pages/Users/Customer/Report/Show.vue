<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

const props = defineProps({
    report: Object,
});

const getTypeLabel = (type) => {
    return (
        {
            illegal_transaction: "Illegal Transaction",
            unfinished_booking: "Unfinished Booking",
        }[type] || type
    );
};

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="View Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-x-4">
                    <HeaderBackButton :url="route('customer.report.index')" />
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800"
                    >
                        Report Details
                    </h2>
                </div>
                <div>
                    <button
                        @click="printReport"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                    >
                        <i class="mr-2 fas fa-print"></i>
                        Print Report
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 space-y-6">
                        <!-- Header Section -->
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Reference #</p>
                                <p class="text-lg font-semibold">
                                    {{ report.id }}
                                </p>
                            </div>
                            <StatusBadge :status="report.status" />
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Reported User
                                </p>
                                <p class="mt-1 font-medium">
                                    {{ report.user.name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">
                                    Date Submitted
                                </p>
                                <p class="mt-1 font-medium">
                                    {{ report.created_at }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Type</p>
                                <span
                                    class="inline-block px-2 py-1 mt-1 text-xs font-medium rounded-full"
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
                            </div>
                        </div>

                        <!-- Complaint Details -->
                        <div>
                            <p class="text-sm text-gray-500">
                                Complaint Details
                            </p>
                            <div class="p-4 mt-1 rounded-lg bg-gray-50">
                                <p class="whitespace-pre-wrap">
                                    {{ report.complaint }}
                                </p>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div v-if="report.attachments?.length">
                            <p class="mb-2 text-sm text-gray-500">
                                Attachments
                            </p>
                            <div class="grid grid-cols-1 gap-2">
                                <a
                                    v-for="file in report.attachments"
                                    :key="file.id"
                                    :href="file.file_path"
                                    target="_blank"
                                    class="flex items-center p-3 space-x-3 rounded-lg bg-gray-50 hover:bg-gray-100"
                                >
                                    <i class="text-gray-400 fas fa-file"></i>
                                    <span class="text-sm text-primary">{{
                                        file.file_name
                                    }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- Admin Response -->
                        <div
                            v-if="report.admin_remarks"
                            class="p-4 border border-gray-200 rounded-lg"
                        >
                            <p class="text-sm font-medium text-gray-500">
                                Admin Response
                            </p>
                            <p class="mt-2 whitespace-pre-wrap">
                                {{ report.admin_remarks }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    /* Hide navigation and buttons when printing */
    nav,
    button,
    .header-actions {
        display: none !important;
    }

    /* Remove background colors and shadows */
    .bg-white {
        background-color: white !important;
        box-shadow: none !important;
    }

    /* Ensure the content is visible */
    .overflow-hidden {
        overflow: visible !important;
    }

    /* Reset padding for print */
    .p-6 {
        padding: 1rem !important;
    }

    /* Hide Modal if it's open */
    .modal {
        display: none !important;
    }

    /* Ensure text is black for better printing */
    * {
        color: black !important;
    }
}
</style>
