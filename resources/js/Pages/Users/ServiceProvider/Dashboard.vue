<script setup>
import { ModalLink } from "@inertiaui/modal-vue";
import { Link, useForm, Head } from "@inertiajs/vue3";
import moment from "moment";
import { ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    PieController,
    BarController,
} from "chart.js";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TableWrapper from "@/Components/Table/TableWrapper.vue";
import TableHeader from "@/Components/Table/TableHeader.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import ActionButton from "@/Components/ActionButton.vue";
import PaginationLinks from "@/Components/PaginationLinks.vue";
import SearchForm from "@/Components/Form/SearchForm.vue";

ChartJS.register(
    BarController,
    PieController,
    BarElement,
    CategoryScale,
    LinearScale,
    ArcElement,
    Title,
    Tooltip,
    Legend
);

const props = defineProps({
    services: Array,
    availServicePending: Array,
    stats: Object,
    recentBookings: Array,
    chartData: Object,
    paymentAccounts: Array, // Add this prop
    paymentTransactions : Array,
});

const headers = ref(["Name", "Date Joined", "Action"]);

const search = () => {
    searchForm.get(route("admin.users.index"));
};

let revenueChart = null;
let bookingStatusChart = null;

onMounted(() => {
    const ctx = document.getElementById("revenueChart");
    revenueChart = new ChartJS(ctx, {
        type: "bar",
        data: props.chartData.monthlyRevenue,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "top",
                },
                title: {
                    display: true,
                    text: "Monthly Revenue",
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            return ` $${context.raw.toFixed(2)}`;
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return "$" + value;
                        },
                    },
                },
            },
        },
    });

    // Initialize Booking Status Chart
    const statusCtx = document.getElementById("bookingStatusChart");
    bookingStatusChart = new ChartJS(statusCtx, {
        type: "pie",
        data: props.chartData.bookingStatus,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: "right",
                },
                title: {
                    display: true,
                    text: "Booking Status Distribution",
                },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const label = context.label || "";
                            const value = context.raw || 0;
                            return `${label}: ${value} bookings`;
                        },
                    },
                },
            },
        },
    });
});

onBeforeUnmount(() => {
    if (revenueChart) revenueChart.destroy();
    if (bookingStatusChart) bookingStatusChart.destroy();
});
</script>

<template>
    <Head title="Service Provider Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Service Provider Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-center mb-5 gap-x-5">
                    <div class="flex-1">
                        <Link
                            :href="route('service-provider.services.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
                            <div>
                                <span>
                                    <i class="fi fi-rr-builder"></i>
                                    Total Services
                                </span>
                            </div>
                            <div class="text-2xl font-black text-primary">
                                {{ services.length ?? 0 }}
                            </div>
                        </Link>
                    </div>
                    <div class="flex-1">
                        <Link
                            :href="route('service-provider.booking.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
                            <div>
                                <span>
                                    <i class="fi fi-rr-briefcase"></i>
                                    Booking
                                </span>
                            </div>
                            <div class="text-2xl font-black text-primary">
                                {{ availServicePending.length ?? 0 }}
                            </div>
                        </Link>
                    </div>
                    <div class="flex-1">
                        <Link
                            :href="route('service-provider.payment-accounts.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
                            <div>
                                <span>
                                    <i class="fi fi-rr-wallet"></i>
                                    Payment Accounts
                                </span>
                            </div>
                            <div class="text-2xl font-black text-primary">
                                {{ paymentAccounts?.length ?? 0 }}
                            </div>
                        </Link>
                    </div>
                    <div class="flex-1">
                        <Link
                            :href="route('service-provider.transactions.index')"
                            class="flex flex-col w-full p-6 bg-white rounded-lg shadow-sm hover:cursor-pointer hover:shadow-lg"
                        >
                            <div>
                                <span>
                                    <i class="fi fi-rr-receipt"></i>
                                    Transactions
                                </span>
                            </div>
                            <div class="text-2xl font-black text-primary">
                                {{ paymentTransactions?.length ?? 0 }}
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div
                    class="grid grid-cols-1 gap-5 mb-8 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">
                            Total Bookings
                        </h3>
                        <p class="mt-2 text-3xl font-semibold text-primary">
                            {{ stats.totalBookings }}
                        </p>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">
                            Pending Requests
                        </h3>
                        <p class="mt-2 text-3xl font-semibold text-yellow-600">
                            {{ stats.pendingBookings }}
                        </p>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">
                            Completed Jobs
                        </h3>
                        <p class="mt-2 text-3xl font-semibold text-green-600">
                            {{ stats.completedBookings }}
                        </p>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <h3 class="text-sm font-medium text-gray-500">
                            Total Revenue
                        </h3>
                        <p class="mt-2 text-3xl font-semibold text-purple-600">
                            ₱{{ stats.totalRevenue.toLocaleString() }}
                        </p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 gap-8 mb-8 lg:grid-cols-2">
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h3 class="mb-4 text-lg font-semibold">
                            Revenue Overview
                        </h3>
                        <div class="h-80">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h3 class="mb-4 text-lg font-semibold">
                            Booking Status Distribution
                        </h3>
                        <div class="h-80">
                            <canvas id="bookingStatusChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Recent Bookings Table -->
                <div class="p-6 bg-white rounded-lg shadow">
                    <h3 class="mb-4 text-lg font-semibold">Recent Bookings</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="p-3">Service</th>
                                    <th class="p-3">Customer</th>
                                    <th class="p-3">Date</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="booking in recentBookings"
                                    :key="booking.id"
                                    class="border-b hover:bg-gray-50"
                                >
                                    <td class="p-3">
                                        {{ booking.service.name }}
                                    </td>
                                    <td class="p-3">{{ booking.user.name }}</td>
                                    <td class="p-3">
                                        {{
                                            moment(booking.created_at).format(
                                                "MMM D, YYYY"
                                            )
                                        }}
                                    </td>
                                    <td class="p-3">
                                        <span
                                            :class="{
                                                'px-2 py-1 text-sm rounded-full': true,
                                                'bg-yellow-100 text-yellow-800':
                                                    booking.status ===
                                                    'pending',
                                                'bg-green-100 text-green-800':
                                                    booking.status ===
                                                    'completed',
                                                'bg-red-100 text-red-800':
                                                    booking.status ===
                                                    'cancelled',
                                            }"
                                        >
                                            {{ booking.status }}
                                        </span>
                                    </td>
                                    <td class="p-3 font-medium">
                                        ${{ booking.total_price.toFixed(2) }}
                                    </td>
                                </tr>
                                <tr v-if="recentBookings.length === 0">
                                    <td
                                        colspan="5"
                                        class="p-3 text-center text-gray-500"
                                    >
                                        No recent bookings found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
