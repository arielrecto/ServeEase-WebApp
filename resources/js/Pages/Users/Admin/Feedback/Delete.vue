<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    feedback: Object,
});

const deleteFeedback = () => {
    router.delete(route("admin.feedbacks.destroy", props.feedback.id), {
        onSuccess: () => {
            modalRef.value.close();
        },
    });
};

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Delete feedback?
        </h2>

        <p>This action cannot be undone.</p>

        <form @submit.prevent="deleteFeedback">
            <div class="flex justify-end">
                <div class="flex gap-x-4">
                    <button
                        @click="modalRef.close"
                        type="button"
                        class="button-ghost"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="button-primary">
                        Confirm
                    </button>
                </div>
            </div>
        </form>
    </Modal>
</template>
