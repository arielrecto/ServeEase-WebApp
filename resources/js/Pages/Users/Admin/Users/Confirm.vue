<script setup>
import { Modal } from "@inertiaui/modal-vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    user: Object,
    action: String,
});

const modalRef = ref(null);

const form = useForm({
    brgyId: null,
});

const handleSubmit = () => {
    if (props.action === "deactivate") {
        router.delete(route("admin.users.destroy", props.user.id), {
            onFinish: () => {
                modalRef.value.close();
            },
        });

        return;
    }

    router.put(route("admin.users.restore", props.user.id), {
        onFinish: () => {
            modalRef.value.close();
        },
    });
};
</script>

<template>
    <Modal ref="modalRef">
        <h2
            v-if="action === 'deactivate'"
            class="text-xl font-semibold leading-tight text-gray-800"
        >
            Deactivate user?
        </h2>
        <h2
            v-else-if="action === 'activate'"
            class="text-xl font-semibold leading-tight text-gray-800"
        >
            Activate user?
        </h2>

        <p v-if="action === 'deactivate'">
            This will suspend the user's account.
        </p>
        <p v-else-if="action === 'activate'">
            This will revoke the user's account suspension.
        </p>

        <form @submit.prevent="deleteBrgy">
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
                        @click="handleSubmit"
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
