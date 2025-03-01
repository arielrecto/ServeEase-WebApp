<script setup>
import { Modal, ModalLink } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
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
            modalRef.value.close();
        },
    });
};

const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-2xl font-semibold">
            {{
                providerProfile.profile.first_name +
                " " +
                providerProfile.profile.last_name
            }}
        </h2>
        <span class="text-gray-600">{{ providerProfile.profile.address }}</span>
        <section class="space-y-4 overflow-y-hidden">
            <h3 class="text-lg font-semibold">Service Provider Profile</h3>
            <div class="flex flex-col gap-y-3">
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Service Offered</span>
                    <span class="text-gray-600">
                        {{ providerProfile.service_type.name }}
                    </span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Years of experience</span>
                    <span class="text-gray-600">{{
                        providerProfile.experience
                    }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Contact Number</span>
                    <span class="text-gray-600">{{
                        providerProfile.contact
                    }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Certificate</span>
                    <div class="w-full">
                        <img
                            :src="providerProfile.certificate"
                            alt="Provider certificate"
                            class="object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>

        <section>
            <button
                class="flex items-center w-full panel-btn panel-btn-accept"
                onclick="my_modal_2.showModal()"
            >
                Approve
            </button>
            <dialog id="my_modal_2" class="modal">
                <div class="modal-box">
                    <form method="dialog">
                        <button
                            class="absolute btn btn-sm btn-circle btn-ghost right-2 top-2"
                        >
                            ✕
                        </button>
                    </form>
                    <div class="space-y-6">
                        <h2
                            class="text-xl font-semibold leading-tight text-gray-800"
                        >
                            Approve this application?
                        </h2>
                        <p>
                            Approving this user means they are an official
                            service provider for ServEase.
                        </p>
                        <div class="modal-action">
                            <form method="dialog">
                                <button class="button-ghost">Cancel</button>
                            </form>
                            <form @submit.prevent="submit">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="button-primary"
                                >
                                    Confirm
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
            <!-- <ModalLinkDialog :href="route('admin.service-provider.approve', providerProfile.id)" @success="modalRef.close" class="sticky flex items-center w-full panel-btn panel-btn-accept">Approve</ModalLinkDialog> -->
            <!-- <ModalLink @success="modalRef.close" :href="route('admin.service-provider.approve', providerProfile.id)" class="sticky flex items-center w-full panel-btn panel-btn-accept">
                Approve
            </ModalLink> -->
        </section>
    </Modal>
</template>
