<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    availService: Object,
});

const form = useForm({
    availServiceId: props.availService.id,
});

const submit = () => {
    form.put(route("customer.booking.cancel", form.availServiceId), {
        onFinish: () => {
            modalRef.value.close();
        }
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

            <p>
                This booking will be cancelled.
            </p>

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
        </div>
    </Modal>
</template>
