<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    id: Number,
    status: String,
});

const form = useForm({
    status: props.status,
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
