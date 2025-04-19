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
    <div class="w-full h-full min-h-96">
        <FullCalendar :options="calendarOptions">
            <template v-slot:eventContent="arg">
                <b>{{ arg.timeText }}</b> <i>{{ arg.event.title }}</i>
            </template>
        </FullCalendar>
    </div>
</template>
