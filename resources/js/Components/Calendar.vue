<script setup>
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { ref, watch } from "vue";
import { useForm } from '@inertiajs/vue3';
import ModalLinkDialog from '@/Components/Modal/ModalLinkDialog.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';


const props = defineProps({
    events: {
        type: Array,
        required: true,
    },
    can_create: {
        type: Boolean,
        default: false
    }
});

const showModal = ref(false);
const selectedDate = ref(null);

const form = useForm({
    event_name: '',
    event_type: 'other',
    start_date: '',
    end_date: '',
    description: ''
});

const calendarOptions = ref({
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: "dayGridMonth",
    events: [],
    selectable: props.can_create,
    dateClick: handleDateClick
});

function handleDateClick(arg) {
    if (!props.can_create) return;

    selectedDate.value = arg.date;
    form.start_date = arg.date.toISOString().split('T')[0];
    form.end_date = arg.date.toISOString().split('T')[0];
    showModal.value = true;
}

const submit = () => {
    form.post(route('service-provider.personal-events.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        }
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
                <b>{{ arg.timeText }}</b>
                <i>{{ arg.event.title }}</i>
            </template>
        </FullCalendar>

        <template v-if="showModal">
            <ModalLinkDialog
                title="Create Event"
                @close="showModal = false"
            >
                <form class="space-y-4">
                    <div>
                        <InputLabel for="event_name" value="Event Name" />
                        <TextInput
                            id="event_name"
                            v-model="form.event_name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel for="event_type" value="Event Type" />
                        <select
                            id="event_type"
                            v-model="form.event_type"
                            class="mt-1 block w-full rounded-md border-gray-300"
                        >
                            <option value="holiday">Holiday</option>
                            <option value="meeting">Meeting</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput
                                id="start_date"
                                v-model="form.start_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>

                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput
                                id="end_date"
                                v-model="form.end_date"
                                type="date"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="description" value="Description" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300"
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
            </ModalLinkDialog>
        </template>
    </div>
</template>
