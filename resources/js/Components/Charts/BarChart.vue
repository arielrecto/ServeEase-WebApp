<script setup>
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { onMounted, onBeforeUnmount, defineProps } from 'vue'

// Register required components
ChartJS.register(
    BarElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    },
    options: {
        type: Object,
        default: () => ({
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        })
    }
})

let chartInstance = null

onMounted(() => {
    const ctx = document.getElementById(props.chartData.id || 'bar-chart')
    chartInstance = new ChartJS(ctx, {
        type: 'bar',
        data: props.chartData,
        options: props.options
    })
})

onBeforeUnmount(() => {
    if (chartInstance) {
        chartInstance.destroy()
    }
})
</script>

<template>
    <div class="h-80">
        <canvas :id="chartData.id || 'bar-chart'"></canvas>
    </div>
</template>

