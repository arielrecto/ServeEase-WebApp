<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    availService: Object,
});

const form = useForm({
    availServiceId: props.availService.id,
    remarks: "", // Changed to match backend expectation
});

const submit = () => {
    form.put(route("customer.booking.cancel", form.availServiceId), {
        preserveScroll: true,
        onFinish: () => {
            modalRef.value.close();
        },
    });
};

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <div
            class="px-6 py-4 space-y-6 overflow-hidden bg-white rounded-md shadow"
        >
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Cancel Booking?
            </h2>

            <div class="space-y-4">
                <p class="text-gray-600">
                    This booking will be cancelled. Please provide a reason for
                    cancellation:
                </p>

                <div class="space-y-2">
                    <textarea
                        v-model="form.remarks"
                        rows="3"
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Enter your reason for cancellation..."
                        required
                    ></textarea>
                    <p
                        v-if="form.errors.remarks"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.remarks }}
                    </p>
                </div>
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
                            :disabled="form.processing || !form.remarks"
                            class="button-primary"
                        >
                            <span v-if="form.processing">Cancelling...</span>
                            <span v-else>Confirm</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </Modal>
</template>
