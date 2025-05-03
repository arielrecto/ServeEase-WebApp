<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { Modal } from "@inertiaui/modal-vue";
import { ref } from 'vue';

const props = defineProps({
    date: String
});

const form = useForm({
    event_name: '',
    event_type: 'other',
    start_date: props.date || '',
    end_date: props.date || '',
    description: ''
});

const modalRef = ref(null);

const submit = () => {
    form.post(route('service-provider.personal-events.store'), {
        preserveScroll: true,
        onSuccess: () => {
            modalRef.value.close();
        }
    });
};
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-lg font-medium mb-4">Create New Event</h2>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="event_name" value="Event Name" />
                <TextInput
                    id="event_name"
                    v-model="form.event_name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                />
                <InputError :message="form.errors.event_name" class="mt-1" />
            </div>

            <div>
                <InputLabel for="event_type" value="Event Type" />
                <select
                    id="event_type"
                    v-model="form.event_type"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary"
                >
                    <option value="holiday">Holiday</option>
                    <option value="meeting">Meeting</option>
                    <option value="other">Other</option>
                </select>
                <InputError :message="form.errors.event_type" class="mt-1" />
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
                    <InputError :message="form.errors.start_date" class="mt-1" />
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
                    <InputError :message="form.errors.end_date" class="mt-1" />
                </div>
            </div>

            <div>
                <InputLabel for="description" value="Description" />
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-primary focus:ring-primary"
                ></textarea>
                <InputError :message="form.errors.description" class="mt-1" />
            </div>

            <div class="flex justify-end gap-x-4 mt-6">
                <button
                    type="button"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                    @click="modalRef.value.close()"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary-dark"
                    :disabled="form.processing"
                >
                    Create Event
                </button>
            </div>
        </form>
    </Modal>
</template>
