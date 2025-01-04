<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    providerProfile: Object,
});

const form = useForm({
    providerProfileId: null,
});

const submit = () => {
    form.put(`/admin/applications/approved/${props.providerProfile.id}`, {
        onFinish: (visit) => {
            modalRef.value.emit("success");
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
                Approve this application?
            </h2>

            <p>
                Approving this user means they are an official service provider
                for ServEase.
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
