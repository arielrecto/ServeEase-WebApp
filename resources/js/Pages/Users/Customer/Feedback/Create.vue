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
    rate: 0,
    content: null,
});

const modalRef = ref(null);

const ratingOptions = [1, 2, 3, 4, 5];

const submit = () => {
    form.post(route("customer.feedbacks.store"), {
        onSuccess: () => {
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
