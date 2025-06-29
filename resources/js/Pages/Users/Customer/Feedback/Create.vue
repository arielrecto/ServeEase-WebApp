<script setup>
import { ref, watch } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { Modal } from "@inertiaui/modal-vue";

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import ImageUpload from "@/Components/Form/ImageUpload.vue";

const props = defineProps(["availService"]);

const form = useForm({
    availServiceId: props?.availService?.id,
    rate: 1,
    content: null,
    attachments: [],
});

const modalRef = ref(null);
const ratingOptions = [1, 2, 3, 4, 5];
const fileUrls = ref(new Map()); // Store file URLs

const handleFileUpload = (e) => {
    const newFiles = Array.from(e.target.files);
    form.attachments = [...form.attachments, ...newFiles];

    // Create URLs for new files
    newFiles.forEach((file) => {
        fileUrls.value.set(file, URL.createObjectURL(file));
    });
};

const removeFile = (index) => {
    const file = form.attachments[index];
    if (fileUrls.value.has(file)) {
        URL.revokeObjectURL(fileUrls.value.get(file));
        fileUrls.value.delete(file);
    }
    form.attachments.splice(index, 1);
};

const viewFile = (file) => {
    let url = fileUrls.value.get(file);
    if (!url) {
        url = URL.createObjectURL(file);
        fileUrls.value.set(file, url);
    }
    window.open(url, "_blank");
};

const getFileIcon = (mimeType) => {
    if (mimeType.startsWith("image/")) return "fa-image";
    return "fa-file";
};

const submit = () => {
    form.post(route("customer.feedbacks.store"), {
        onSuccess: () => {
            // Clean up URLs before resetting form
            fileUrls.value.forEach((url) => URL.revokeObjectURL(url));
            fileUrls.value.clear();
            form.reset();
            modalRef.value.close();
        },
    });
};
</script>

<template>
    <Modal ref="modalRef">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Write a Review
        </h2>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex flex-col items-center justify-center">
                <div class="gap-x-5 rating rating-lg">
                    <input
                        v-for="rating in ratingOptions"
                        v-model="form.rate"
                        name="rating"
                        :value="rating"
                        type="radio"
                        class="bg-primary mask mask-star-2"
                        :checked="form.rate === rating"
                    />
                </div>

                <InputError class="mt-2" :message="form.errors.rate" />
            </div>

            <div>
                <InputLabel :value="Comment" />
                <textarea
                    v-model="form.content"
                    rows="8"
                    class="w-full border-gray-300 rounded-md shadow-sm resize-none focus:border-primary focus:ring-primary"
                    placeholder="Tell us your experience with the service"
                ></textarea>
                <InputError class="mt-2" :message="form.errors.content" />
            </div>

            <div>
                <InputLabel :value="'Attachments'" />
                <input
                    type="file"
                    multiple
                    accept="image/png, image/jpeg"
                    @change="handleFileUpload"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring-primary"
                />
                <InputError class="mt-2" :message="form.errors.attachments" />
            </div>

            <div>
                <div class="flex flex-wrap gap-4">
                    <div
                        v-for="(file, index) in form.attachments"
                        :key="index"
                        class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg group hover:bg-gray-200"
                    >
                        <i
                            :class="[
                                'fa-solid',
                                getFileIcon(file.type),
                                'text-gray-500 group-hover:text-gray-700',
                            ]"
                        ></i>
                        <span class="truncate max-w-[150px]">{{
                            file.name
                        }}</span>
                        <div class="flex gap-2 ml-2">
                            <a
                                href="#"
                                @click.prevent="viewFile(file)"
                                class="text-primary hover:text-primary/80"
                                title="View file"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <button
                                type="button"
                                @click="removeFile(index)"
                                class="text-red-500 hover:text-red-600"
                                title="Remove file"
                            >
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing"
                    >Submit
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </Modal>
</template>
