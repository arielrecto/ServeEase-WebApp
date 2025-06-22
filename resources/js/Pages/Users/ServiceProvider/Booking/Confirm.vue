<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    id: Number,
    status: String,
});

const rejectionReasons = [
    { id: 1, reason: "Schedule conflict" },
    { id: 2, reason: "Service unavailable" },
    { id: 3, reason: "Location out of service area" },
    { id: 4, reason: "Insufficient booking details" },
    { id: 5, reason: "Service capacity full" },
    { id: 6, reason: "Equipment/tools unavailable" },
    { id: 7, reason: "Other" },
];

const form = useForm({
    status: props.status,
    remark: "",
    otherRemark: "",
});

const submit = () => {
    form.put(route("service-provider.booking.update.status", props.id), {
        onFinish: () => {
            modalRef.value.close();
        },
    });
};

const headingText = computed(() => {
    return {
        cancelled: "Cancel booking?",
        confirmed: "Confirm booking?",
        rejected: "Reject booking?",
        in_progress: "Start the service?",
        completed: "Service done?",
    }[props.status];
});

const dialogText = computed(() => {
    return {
        cancelled: "This will cancel the booking.",
        confirmed: "You are about to accept this request.",
        rejected: "You are about to decline this request.",
        in_progress: "You are about to start the service.",
        completed: "This will set the booking as completed.",
    }[props.status];
});

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ headingText }}
        </h2>

        <p>{{ dialogText }}</p>

        <div v-if="status === 'rejected'">
            <label class="block text-sm font-medium text-gray-700">
                Reason for Rejection
            </label>
            <select
                v-model="form.remark"
                class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary"
                required
            >
                <option value="" disabled selected>Select a reason...</option>
                <option
                    v-for="reason in rejectionReasons"
                    :key="reason.id"
                    :value="reason.reason"
                >
                    {{ reason.reason }}
                </option>
            </select>
            <textarea
                v-if="form.remark === 'Other'"
                v-model="form.otherRemark"
                rows="4"
                class="w-full mt-2 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary"
                placeholder="Please specify the reason..."
                required
            ></textarea>
        </div>

        <form @submit.prevent="submit">
            <div class="flex justify-end">
                <div class="flex gap-x-4">
                    <button
                        @click="modalRef.close"
                        type="button"
                        class="button-ghost"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="button-primary"
                    >
                        Confirm
                    </button>
                </div>
            </div>
        </form>
    </Modal>
</template>
