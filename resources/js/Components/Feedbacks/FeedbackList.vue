<script setup>
import { reactive } from "vue";
import moment from "moment";
import axios from "axios";

import SelectInput from "@/Components/Form/SelectInput.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

import { useLoader } from "../../Composables/loader";

const props = defineProps({
    service: Object,
    filterShown: {
        type: Boolean,
        default: true,
    },
});

const state = reactive({
    feedbacks: [],
    form: { rating: null },
});

const { setIsLoading } = useLoader();

const ratingOptions = [5, 4, 3, 2, 1];

const fetchFeedbacks = async () => {
    setIsLoading(true);
    try {
        const res = await axios.get(
            route("customer.services.feedback", props.service.id),
            {
                params: { rating: state.form.rating },
            }
        );

        state.feedbacks = [...res.data];
    } catch (e) {
        console.log(e);
    }
    setIsLoading(false);
};

(() => {
    fetchFeedbacks();
})();
</script>

<template>
    <SelectInput
        v-model="state.form.rating"
        class="mt-1 text-black"
        :placeholder="'Rating'"
        required
        @update-value="fetchFeedbacks"
    >
        <option v-for="rating in ratingOptions" :key="rating" :value="rating">
            {{ rating }}
        </option>
    </SelectInput>

    <template v-if="state.feedbacks.length > 0">
        <div class="py-2 pr-5 space-y-5 divide-y divide-gray-300">
            <div v-for="feedback in state.feedbacks" class="flex pt-2 gap-x-4">
                <div>
                    <div
                        class="w-8 bg-gray-600 rounded-full aspect-square"
                    ></div>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-x-3">
                        <span class="inline-block mr-3 text-primary"
                            >John Doe</span
                        >
                        <span class="inline-block mr-3 text-sm">
                            <i class="text-yellow-500 fa-solid fa-star"></i>
                            {{ feedback.rate }}
                        </span>
                        <span class="text-sm italic text-gray-600">{{
                            moment(feedback.created_at).format("LL")
                        }}</span>

                        <div
                            v-if="$page.props.auth.isAdmin"
                            class="relative ms-3"
                        >
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none"
                                        >
                                            <i
                                                class="fa-solid fa-ellipsis-vertical"
                                            ></i>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink
                                        :href="
                                            route(
                                                'admin.feedbacks.edit',
                                                feedback.id
                                            )
                                        "
                                    >
                                        <span class="inline-flex gap-x-2">
                                            <i class="ri-edit-2-line"></i>
                                            Edit
                                        </span>
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="
                                            route(
                                                'admin.feedbacks.delete',
                                                feedback.id
                                            )
                                        "
                                        isModalLink
                                    >
                                        <span
                                            class="text-error inline-flex gap-x-2"
                                        >
                                            <i class="ri-delete-bin-line"></i>
                                            Delete
                                        </span>
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                    <div class="overflow-y-auto leading-relaxed max-h-28">
                        {{ feedback.content }}
                    </div>
                </div>
            </div>
        </div>
    </template>
    <div v-else class="py-2">
        <p class="text-center text-gray-600">There are no reviews.</p>
    </div>
</template>
