<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement, BarController, PieController } from 'chart.js';
import PieChart from '@/Components/Charts/PieChart.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';

// Register Chart.js components
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
    stats: Object,
    chartData: Object
});

// Chart references
let barChart = null;
let pieChart = null;

onMounted(() => {
    // Initialize Bar Chart
    const barCtx = document.getElementById('monthlyServicesChart');
    barChart = new ChartJS(barCtx, {
        type: 'bar',
        data: props.chartData.monthlyServices,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Initialize Pie Chart
    const pieCtx = document.getElementById('serviceStatusChart');
    pieChart = new ChartJS(pieCtx, {
        type: 'pie',
        data: props.chartData.serviceStatusDistribution,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

onBeforeUnmount(() => {
    if (barChart) barChart.destroy();
    if (pieChart) pieChart.destroy();
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Admin Dashboard</h2>
        </template>

        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="p-6 bg-white rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Total Services</h3>
                    <div class="text-4xl font-bold text-blue-600">
                        {{ stats.totalServices.toLocaleString() }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Monthly Services Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Monthly Services</h3>
                    <div class="h-80">
                        <canvas id="monthlyServicesChart"></canvas>
                    </div>
                </div>

                <!-- Service Status Distribution -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Service Status Distribution</h3>
                    <div class="h-80">
                        <canvas id="serviceStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Active Users</h3>
                    <div class="text-3xl font-bold text-green-600">
                        {{ stats.activeUsers.toLocaleString() }}
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Revenue</h3>
                    <div class="text-3xl font-bold text-purple-600">
                         ₱{{ stats.revenue.toLocaleString() }}
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-2">Ongoing Services</h3>
                    <div class="text-3xl font-bold text-yellow-600">
                        {{ stats.ongoingServices.toLocaleString() }}
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
