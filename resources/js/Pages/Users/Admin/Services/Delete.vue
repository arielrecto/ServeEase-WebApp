<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    service: Object,
});

const form = useForm({
    remark: '',
});


console.log(props.providerProfile);

const modalRef = ref(null);


const submit = () => {
    form.delete(route("admin.services.destroy", props.service.id), {
        onSuccess: () => {
            modalRef.value.close();
        },
    });
};
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Delete Service
        </h2>

        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Are you sure you want to delete this service?
                </label>
            </div>

            <div class="flex justify-end gap-x-4">
                <button @click="modalRef.close" type="button" class="button-ghost">
                    Cancel
                </button>
                <PrimaryButton type="submit" :disabled="form.processing" class="button-error">
                    Confirm
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
