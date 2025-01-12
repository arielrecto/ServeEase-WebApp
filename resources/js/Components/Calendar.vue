<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { ref, watch } from "vue";

const props = defineProps({
    events: {
        type: Array,
        required: true,
    },
});

const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    events: [],
});

watch(
    () => props.events,
    (newEvents) => {
        calendarOptions.value.events = newEvents;
    },
    { immediate: true }
);
</script>

<template>
    <div class="h-full w-full min-h-96">
        <FullCalendar :options="calendarOptions" />
    </div>
</template>
