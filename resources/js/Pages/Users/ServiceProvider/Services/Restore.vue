<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    service: Object,
});

const form = useForm({});
const modalRef = ref(null);

const submit = () => {
    form.post(route("service-provider.services.restore", props.service.id), {
        onSuccess: () => {
            modalRef.value.close();
        },
    });
};
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Restore Service
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Are you sure you want to restore this service? The service will be
            visible to customers again.
        </p>
        <form @submit.prevent="submit" class="mt-6">
            <div class="flex justify-end gap-x-4">
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
                    Restore Service
                </button>
            </div>
        </form>
    </Modal>
</template>
