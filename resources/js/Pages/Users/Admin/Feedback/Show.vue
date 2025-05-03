<script setup>
import { ref } from "vue";
import moment from "moment";

import { Modal } from "@inertiaui/modal-vue";
import { Link } from "@inertiajs/vue3";
import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";

const props = defineProps(["feedback"]);
const modalRef = ref(null);
</script>

<template>
    <Modal ref="modalRef">
        <div class="space-y-4">
            <div class="space-y-1">
                <div class="text-2xl font-bold">
                    {{ feedback.avail_service.service.name }}
                </div>
                <div>
                    <span class="text-gray-600">Service Provider: </span
                    >{{ feedback.avail_service.service.user.name }}
                </div>
                <div>
                    <span class="text-gray-600">Reviewed by: </span
                    >{{ feedback.user.name }}
                </div>
            </div>

            <div>
                <span class="inline-block mr-3 text-sm">
                    <i class="text-yellow-500 fa-solid fa-star"></i>
                    {{ feedback.rate }}
                </span>
            </div>
            <div class="space-y-1">
                <div>Comment:</div>
                <div class="text-gray-600">
                    {{ feedback.content }}
                </div>
            </div>

            <!-- Add attachments section -->
            <div
                v-if="feedback.attachments && feedback.attachments.length > 0"
                class="space-y-2"
            >
                <div>Attachments:</div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <a
                        v-for="attachment in feedback.attachments"
                        :key="attachment.id"
                        v-if="attachment.mime_type.startsWith('image/')"
                        :href="`/storage/${attachment.file_path}`"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="relative overflow-hidden transition-opacity rounded-lg aspect-square hover:opacity-90"
                    >
                        <img
                            :src="`/storage/${attachment.file_path}`"
                            :alt="attachment.file_name"
                            class="object-cover w-full h-full"
                        />
                    </a>
                </div>
            </div>

            <div class="flex items-center mt-10 gap-x-5">
                <Link
                    @click="modalRef.close"
                    :href="route('admin.feedbacks.edit', feedback.id)"
                    class="inline-flex items-center text-primary gap-x-1"
                    ><i class="fi fi-br-file-edit"></i>Edit</Link
                >

                <ModalLinkDialog
                    :href="route('admin.feedbacks.delete', feedback.id)"
                    class="text-red-600"
                    ><i class="fi fi-rs-trash"></i>Delete</ModalLinkDialog
                >
            </div>
        </div>
    </Modal>
</template>
