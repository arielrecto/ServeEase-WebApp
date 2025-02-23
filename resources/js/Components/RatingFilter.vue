<script setup>
import { ref } from "vue";
const rating = ref(0);

const emit = defineEmits(["update:rating"]);

function updateRating() {
    emit("update:rating", rating.value);
}
</script>

<template>
    <div class="flex items-center mb-4">
        <label for="rating" class="block text-sm font-medium text-gray-700"
            >Rating:</label
        >
        <select
            id="rating"
            v-model="rating"
            @change="updateRating"
            class="block w-full py-2 pl-10 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <option value="0">All</option>
            <option value="1">1+</option>
            <option value="2">2+</option>
            <option value="3">3+</option>
            <option value="4">4+</option>
            <option value="5">5</option>
        </select>
    </div>
</template>
