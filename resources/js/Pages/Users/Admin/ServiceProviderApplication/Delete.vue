<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";

const rejectionReasons = [
    { id: 2, reason: "Invalid credentials" },
    { id: 3, reason: "Insufficient experience" },
    { id: 4, reason: "Unverifiable information" },
    { id: 5, reason: "Blurry, cropped photo, etc." },
    { id: 6, reason: "Other" },
];

const props = defineProps({
    providerProfile: Object,
});

const form = useForm({
    remark: "",
    otherRemark: "",
});

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
                <select
                    v-model="form.remark"
                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary"
                    required
                >
                    <option value="">Select a reason...</option>
                    <option
                        v-for="reason in rejectionReasons"
                        :key="reason.id"
                        :value="reason.reason"
                    >
                        {{ reason.reason }}
                    </option>
                </select>
            </div>

            <div v-if="form.remark === 'Other'">
                <label class="block text-sm font-medium text-gray-700">
                    Specify Other Reason
                </label>
                <textarea
                    v-model="form.otherRemark"
                    rows="4"
                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:border-primary focus:ring-primary"
                    placeholder="Please specify the reason for rejection..."
                ></textarea>
            </div>

            <div class="flex justify-end gap-x-4">
                <button
                    @click="modalRef.close"
                    type="button"
                    class="button-ghost"
                >
                    Cancel
                </button>
                <PrimaryButton
                    type="submit"
                    :disabled="form.processing || !form.remark"
                    class="button-error"
                >
                    Reject
                </PrimaryButton>
            </div>
        </form>
    </Modal>
</template>
