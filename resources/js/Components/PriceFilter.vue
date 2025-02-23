<script setup>
import { ref } from "vue";
const priceRange = ref([0, 100]);

const emit = defineEmits(["update:price-range"]);

function updatePriceRange() {
    emit("update:price-range", priceRange.value);
}
</script>

// PriceFilter.vue
<template>
    <div class="flex items-center mb-4">
        <label for="price-range" class="block text-sm font-medium text-gray-700"
            >Price Range:</label
        >
        <input
            type="range"
            id="price-range"
            v-model="priceRange"
            @input="updatePriceRange"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
        />
        <span class="ml-2 text-sm text-gray-700">{{ priceRange }}</span>
    </div>
</template>
