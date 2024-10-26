<script setup>
import { Modal } from '@inertiaui/modal-vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import moment from 'moment';

const props = defineProps(['service']);

const form = useForm({
    providerProfileId: null,
});

const deleteService = () => {
    form.delete(route('admin.service-types.destroy', props.service.id), {
        onFinish: visit => {
            modalRef.value.close();
        },
    });
}

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-2xl font-semibold">{{ service.name }}</h2>
        <section class="space-y-4 overflow-y-hidden">
            <!-- <h3 class="text-lg font-semibold">Service Provider Profile</h3> -->
            <div class="flex flex-col gap-y-3">
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Thumbnail</span>
                    <div class="aspect-video overflow-hidden">
                        <img :src="service.thumbnail" alt="Service thumbnail" class="object-cover">
                    </div>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Description</span>
                    <span class="text-gray-600">{{ service.description }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Date created</span>
                    <span class="text-gray-600">{{ moment(service.created_at).format('ll') }}</span>
                </div>
            </div>
        </section>
        <div class="flex gap-x-8">
            <Link :href="route('admin.service-types.edit', service.id)" class="text-primary hover:underline inline-flex gap-x-1 items-center"><i class="fi fi-br-file-edit"></i>Edit</Link>

            <button onclick="my_modal_2.showModal()" class="text-error hover:underline inline-flex gap-x-1 items-center"><i class="fi fi-rs-trash"></i>Delete</button>
            <dialog id="my_modal_2" class="modal">
                <div class="modal-box">
                    <form method="dialog">
                        <button class="absolute btn btn-sm btn-circle btn-ghost right-2 top-2">✕</button>
                    </form>
                    <div class="space-y-6">
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">Delete service?</h2>
                        <p>This action cannot be undone.</p>
                        <div class="modal-action">
                            <form method="dialog">
                                <button class="button-ghost">Cancel</button>
                            </form>
                            <form @submit.prevent="deleteService">
                                <button type="submit" :disabled="form.processing" class="button-primary">Confirm</button>
                            </form>
                        </div>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
        </div>
    </Modal>
</template>
