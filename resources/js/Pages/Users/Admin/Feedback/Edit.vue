<script setup>
import { Head, usePage, useForm } from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";

const props = defineProps({
    feedback: Object,
});

const form = useForm({
    rate: props.feedback.rate,
    content: props.feedback.content,
});

const ratingOptions = [1, 2, 3, 4, 5];

const submit = () => {
    form.put(route("admin.feedbacks.update", props.feedback.id));
};
</script>

<template>
    <Head title="Edit" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('admin.feedbacks.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Feedback
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="p-4 bg-white shadow sm:p-8 sm:rounded-lg">
                    <div class="flex items-center gap-x-2">
                        <span>Author: </span>
                        <div class="w-4 h-4 rounded-full overflow-hidden">
                            <img
                                :src="feedback.user.profile.avatar"
                                alt=""
                                class="object-cover object-center w-full h-full"
                            />
                        </div>
                        <span class="font-bold">{{ feedback.user.name }}</span>
                    </div>
                    <form @submit.prevent="submit" class="mt-6 space-y-6">
                        <div class="flex flex-col items-center justify-center">
                            <div class="gap-x-5 rating rating-lg">
                                <input
                                    v-for="rating in ratingOptions"
                                    v-model="form.rate"
                                    :value="rating"
                                    type="radio"
                                    class="bg-primary mask mask-star-2 cursor-default"
                                    :checked="form.rate === rating"
                                    disabled
                                />
                            </div>
                        </div>

                        <div>
                            <InputLabel :value="Comment" />
                            <textarea
                                v-model="form.content"
                                rows="8"
                                class="w-full border-gray-300 rounded-md shadow-sm resize-none focus:border-primary focus:ring-primary"
                                placeholder="Tell us your experience with the service"
                            ></textarea>
                            <InputError
                                class="mt-2"
                                :message="form.errors.content"
                            />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing"
                                >Save</PrimaryButton
                            >
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
