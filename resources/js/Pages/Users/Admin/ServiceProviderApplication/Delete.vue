<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    providerProfile: Object,
});

const form = useForm({
    remark: '',
});


console.log(props.providerProfile);

const modalRef = ref(null);


const submit = () => {
    form.delete(route("admin.applications.reject", props.providerProfile.id), {
        onSuccess: () => {
            // modalValue.value.emit("success");
            modalRef.value.close();
        },
    });
};
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Reject Application
        </h2>

        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Reason for Rejection
                </label>
                <textarea v-model="form.remark" rows="4"
                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary"
                    placeholder="Please provide a reason for rejection..." required></textarea>
            </div>

            <div class="flex justify-end gap-x-4">
                <button @click="modalRef.close" type="button" class="button-ghost">
                    Cancel
                </button>
                <PrimaryButton type="submit" :disabled="form.processing || !form.remark" class="button-error">
                    Reject
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
