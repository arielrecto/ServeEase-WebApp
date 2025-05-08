<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { ref, watch } from "vue";
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
</script>

<template>
    <div class="w-full h-full min-h-96">
        <FullCalendar :options="calendarOptions">
            <template v-slot:eventContent="arg">
                <i>{{ arg.event.title }}</i>
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
