<script setup>
import { Modal } from '@inertiaui/modal-vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    providerProfile: Object,
});

const form = useForm({
    providerProfileId: null,
});

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Delete record?</h2>

        <p>This action cannot be undone.</p>

        <form @submit.prevent="form.delete(`/admin/service-provider/${providerProfile.id}`)">
            <div class="flex justify-end">
                <div class="flex gap-x-4">
                    <button @click="modalRef.close" type="button" class="button-ghost">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="button-primary">Confirm</button>
                </div>
            </div>
        </form>
    </Modal>
</template>
