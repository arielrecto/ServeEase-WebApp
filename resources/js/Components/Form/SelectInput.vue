<script setup>
defineProps({
    placeholder: {
        default: 'Select an option',
        type: String
    },
    choices: {
        type: [Array, Object],
        required: false,
    }
})
</script>

<template>
    <select v-model="model" class="text-gray-900 border-gray-300 select focus:border-primary focus:ring-primary rounded-xl">
        <option disabled>{{ placeholder }}</option>
        <template v-if="choices">
            <option v-for="choice in choices" :value="choice">{{ choice }}</option>
        </template>

        <!-- Additional options -->
        <slot />
    </select>
</template>
