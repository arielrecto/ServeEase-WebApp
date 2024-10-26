<script setup>
import { Modal } from '@inertiaui/modal-vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    brgy: Object,
});

const form = useForm({
    brgyId: null,
});

const deleteBrgy = () => {
    form.delete(route('admin.barangays.destroy', props.brgy.id), {
        onFinish: () => { modalRef.value.close(); }
    })
};

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Delete record?</h2>

        <p>This action cannot be undone.</p>

        <form @submit.prevent="deleteBrgy">
            <div class="flex justify-end">
                <div class="flex gap-x-4">
                    <button @click="modalRef.close" type="button" class="button-ghost">Cancel</button>
                    <button type="submit" :disabled="form.processing" class="button-primary">Confirm</button>
                </div>
            </div>
        </form>
    </Modal>
</template>
