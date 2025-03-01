<script setup>
import { computed } from "vue";
const props = defineProps({
    status: String,
});

const statusClass = computed(() => {
    return {
        cancelled: "bg-red-100 text-red-800",
        pending: "bg-yellow-100 text-yellow-800",
        in_progress: "bg-orange-100 text-orange-800",
        confirmed: "bg-green-100 text-green-800",
        completed: "bg-green-100 text-green-800",
    }[props.status];
});

const statusText = computed(() => {
    return {
        cancelled: "Cancelled",
        pending: "Pending",
        in_progress: "In Progress",
        confirmed: "Confirmed",
        completed: "Completed",
    }[props.status];
});
</script>

<template>
    <div
        :class="[
            'px-3 py-1 text-sm font-bold rounded-lg text-center',
            statusClass,
        ]"
    >
        {{ statusText }}
    </div>
</template>
