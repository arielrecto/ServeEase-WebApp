<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { ref, watch, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import moment from "moment";

const props = defineProps({
    events: {
        type: Array,
        required: true,
    },
    can_create: {
        type: Boolean,
        default: false,
    },
    onEventClick: {
        type: Function,
        default: null,
    },
});

const showModal = ref(false);
const selectedDate = ref(null);

const form = useForm({
    event_name: "",
    event_type: "other",
    start_date: "",
    end_date: "",
    description: "",
});

const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    events: [],
    selectable: props.can_create,
    dateClick: handleDateClick,
    eventClick: handleEventClick, // Add event click handler
    eventDidMount: function (info) {
        // Add cursor pointer to events
        info.el.style.cursor = "pointer";
    },
});

function handleDateClick(arg) {
    if (!props.can_create) return;

    if (moment(arg.date).isBefore(moment().startOf("day"))) {
        return;
    }

    selectedDate.value = arg.date;
    form.start_date = moment(arg.date).format("YYYY-MM-DD");
    form.end_date = moment(arg.date).format("YYYY-MM-DD");
    showModal.value = true;
}

// Add event click handler function
function handleEventClick(info) {
    if (props.onEventClick) {
        props.onEventClick(info.event);
    }
}

const submit = () => {
    form.post(route("service-provider.personal-events.store"), {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

watch(
    () => props.events,
    (newEvents) => {
        calendarOptions.value.events = newEvents;
    },
    { immediate: true }
);

const renderEventContent = (eventInfo) => {
    return {
        html: `
            <div class="p-2 hover:bg-gray-50">
                <div class="font-medium">${eventInfo.event.title}</div>
                <div class="text-xs text-gray-600">
                    ${eventInfo.event.extendedProps.time || ""}
                </div>
            </div>
        `,
    };
};

const getStatusBadgeClass = (status) => {
    return {
        cancelled: "bg-red-100 text-red-800",
        rejected: "bg-red-100 text-red-800",
        pending: "bg-yellow-100 text-yellow-800",
        in_progress: "bg-orange-100 text-orange-800",
        confirmed: "bg-green-100 text-green-800",
        completed: "bg-green-100 text-green-800",
        approved: "bg-green-100 text-green-800", // Add this line
        resolved: "bg-blue-100 text-blue-800",
    }[status];
};

const getStatusText = (status) => {
    return {
        cancelled: "Cancelled",
        rejected: "Rejected",
        pending: "Pending",
        in_progress: "In Progress",
        confirmed: "Confirmed",
        completed: "Completed",
        approved: "Approved", // Add this line
        resolved: "Resolved",
    }[status];
};

onMounted(() => {
    calendarOptions.value.events = props.events.map((event) => ({
        ...event,
        start: moment(event.start).format("YYYY-MM-DD"),
        end: moment(event.end).format("YYYY-MM-DD"),
        display: "block",
    }));

    renderEventContent;
});
</script>

<template>
    <div class="w-full h-full min-h-96">
        <FullCalendar :options="calendarOptions">
            <template v-slot:eventContent="arg">
                <div class="fc-event-wrap event-title">
                    <i>{{ arg.event.title }}</i>
                    <p
                        v-if="arg.event.extendedProps?.status"
                        :class="
                            getStatusBadgeClass(arg.event.extendedProps.status)
                        "
                    >
                        {{ getStatusText(arg.event.extendedProps?.status) }}
                    </p>
                </div>
            </template>
        </FullCalendar>

        <template v-if="showModal">
            <div class="fixed inset-0 z-50 overflow-y-auto" v-if="showModal">
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 transition-opacity bg-black bg-opacity-50"
                    @click="showModal = false"
                ></div>

                <!-- Modal Panel -->
                <div class="flex items-center justify-center min-h-full p-4">
                    <div
                        class="relative px-4 pt-5 pb-4 overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium">Create Event</h3>
                            <button
                                @click="showModal = false"
                                class="text-gray-400 rounded-md hover:text-gray-500"
                            >
                                <span class="sr-only">Close</span>
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <form class="space-y-4">
                            <div>
                                <InputLabel
                                    for="event_name"
                                    value="Event Name"
                                />
                                <TextInput
                                    id="event_name"
                                    v-model="form.event_name"
                                    type="text"
                                    class="block w-full mt-1"
                                    required
                                />
                            </div>

                            <div>
                                <InputLabel
                                    for="event_type"
                                    value="Event Type"
                                />
                                <select
                                    id="event_type"
                                    v-model="form.event_type"
                                    class="block w-full mt-1 border-gray-300 rounded-md"
                                >
                                    <option value="holiday">Holiday</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel
                                        for="start_date"
                                        value="Start Date"
                                    />
                                    <TextInput
                                        id="start_date"
                                        :min="
                                            new Date()
                                                .toISOString()
                                                .split('T')[0]
                                        "
                                        v-model="form.start_date"
                                        type="date"
                                        class="block w-full mt-1"
                                        disabled
                                        required
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="end_date"
                                        value="End Date"
                                    />
                                    <TextInput
                                        id="end_date"
                                        :min="form.start_date"
                                        v-model="form.end_date"
                                        type="date"
                                        class="block w-full mt-1"
                                        required
                                    />
                                </div>
                            </div>

                            <div>
                                <InputLabel
                                    for="description"
                                    value="Description"
                                />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="block w-full mt-1 border-gray-300 rounded-md"
                                ></textarea>
                            </div>

                            <div class="flex justify-end gap-x-4">
                                <button
                                    type="button"
                                    class="btn btn-ghost"
                                    @click="showModal = false"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    @click.prevent="submit"
                                >
                                    Create Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<style scoped>
.fc-event-wrap {
    font-size: 12px;
    line-height: 1.2;
    white-space: normal;
    max-width: 100%;
    overflow: hidden;
}
/* .event-title {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
} */
</style>
