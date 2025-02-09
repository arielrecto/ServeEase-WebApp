<script setup>
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale } from 'chart.js'
import { onMounted, onBeforeUnmount, defineProps } from 'vue'

// Register required components
ChartJS.register(
    ArcElement,
    CategoryScale,
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
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        })
    }
})

let chartInstance = null

onMounted(() => {
    const ctx = document.getElementById(props.chartData.id || 'pie-chart')
    chartInstance = new ChartJS(ctx, {
        type: 'pie',
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
        <canvas :id="chartData.id || 'pie-chart'"></canvas>
    </div>
</template>
