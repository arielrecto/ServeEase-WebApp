<script setup>
import { ref, watch } from "vue";

import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    placeholder: {
        type: String,
        default: "Search something",
    },
    initialValue: {
        type: String,
        default: "",
    },
});
const emits = defineEmits(["submitted"]);

const query = ref(props.initialValue);

// Watch for external resets
watch(
    () => props.initialValue,
    (newValue) => {
        query.value = newValue;
    }
);
</script>

<template>
    <form
        @submit.prevent="emits('submitted', query)"
        class="w-full md:max-w-md md:mx-auto"
    >
        <label
            for="default-search"
            class="mb-2 text-sm font-medium text-gray-900 sr-only"
            >Search</label
        >
        <div class="relative flex items-center gap-x-2">
            <div
                class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-3"
            >
                <svg
                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 20 20"
                >
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                    />
                </svg>
            </div>
            <input
                type="search"
                v-model="query"
                id="default-search"
                class="block w-full px-4 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg ps-10 bg-gray-50 focus:ring-primary focus:border-primary"
                :placeholder="placeholder"
                required
            />
            <PrimaryButton>Search</PrimaryButton>
        </div>
    </form>
</template>
