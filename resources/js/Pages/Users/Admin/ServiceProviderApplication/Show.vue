<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import ModalLinkSlideover from "@/Components/Modal/ModalLinkSlideover.vue";
import { Modal, ModalLink } from "@inertiaui/modal-vue";
import { useForm } from "@inertiajs/vue3";
import FileView from "@/Components/FileView.vue";

const props = defineProps({
    providerProfile: Object,
});

const form = useForm({
    providerProfileId: null,
});

const approve = () => {
    form.put(`/admin/applications/approved/${props.providerProfile.id}`, {
        onFinish: (visit) => {
            modalRef.value.close();
        },
    });
};

const reject = () => {
    form.delete(route("admin.applications.reject", props.providerProfile.id), {
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
            {{ providerProfile.profile.full_name }}
        </h2>
        <span class="text-gray-600">{{ providerProfile.profile.address }}</span>
        <section class="space-y-4 max-h-[70vh] overflow-y-auto">
            <h3 class="text-lg font-semibold">Provider Information</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Service Offered</span>
                    <span class="text-gray-600">
                        {{ providerProfile.service_type.name }}
                    </span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Years of experience</span>
                    <span class="text-gray-600">{{
                        providerProfile.experience +
                        " " +
                        providerProfile.experience_duration
                    }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Contact Number</span>
                    <span class="text-gray-600">{{
                        providerProfile.contact
                    }}</span>
                </div>
            </div>

            <h3 class="text-lg font-semibold">Submitted Documents</h3>
            <div class="space-y-3">
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold">Valid ID Selected</span>
                    <span class="text-gray-600">{{
                        providerProfile.valid_id_type
                    }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold"
                        >Valid ID photo (Click to view)</span
                    >
                    <a :href="providerProfile.valid_id_image" target="_blank">
                        <img
                            :src="providerProfile.valid_id_image"
                            alt="Valid ID image"
                            class="object-cover"
                        />
                    </a>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold"
                        >Citizenship Document Selected</span
                    >
                    <span class="text-gray-600">{{
                        providerProfile.citizenship_document_type
                    }}</span>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold"
                        >Citizenship Document photo (Click to view)</span
                    >
                    <div class="w-full">
                        <a
                            :href="providerProfile.citizenship_document_image"
                            target="_blank"
                        >
                            <img
                                :src="
                                    providerProfile.citizenship_document_image
                                "
                                alt="Citizenship document image"
                                class="object-cover"
                            />
                        </a>
                    </div>
                </div>
                <div class="flex flex-col gap-y-1">
                    <span class="font-semibold"
                        >Proof document (Click to view)</span
                    >
                    <div class="w-full">
                        <a
                            :href="providerProfile.proof_document_image"
                            target="_blank"
                        >
                            <img
                                :src="providerProfile.proof_document_image"
                                alt="Provider certificate"
                                class="object-cover"
                            />
                        </a>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col gap-y-1"
                v-if="providerProfile.attachments?.length"
            >
                <span class="font-semibold">Additional Documents</span>
                <div class="grid grid-cols-1 gap-4">
                    <FileView
                        v-for="file in providerProfile.attachments"
                        :key="file.id"
                        :file="file"
                        :show-delete="false"
                    />
                </div>
            </div>
        </section>

        <section>
            <div class="flex items-center gap-2">
                <button
                    class="flex items-center w-full panel-btn panel-btn-accept"
                    onclick="my_modal_2.showModal()"
                >
                    Approve
                </button>
                <ModalLink
                    @success="modalRef.close"
                    :href="
                        route('admin.applications.delete', providerProfile.id)
                    "
                    class="flex items-center w-full panel-btn panel-btn-reject"
                >
                    Reject
                </ModalLink>
                <!-- <button class="flex items-center w-full panel-btn panel-btn-reject" onclick="modal_reject.showModal()">
                    Reject
                </button> -->
            </div>
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
                            <form @submit.prevent="approve">
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
            <dialog id="modal_reject" class="modal">
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
                            Reject this application?
                        </h2>
                        <p>This will reject this user's application.</p>
                        <div class="modal-action">
                            <form method="dialog">
                                <button class="button-ghost">Cancel</button>
                            </form>
                            <form @submit.prevent="reject">
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
