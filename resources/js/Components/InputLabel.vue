<script setup>
defineProps({
    value: {
        type: String,
    },
    isOptional: {
        default: false,
        type: Boolean,
    }
});
</script>

<template>
    <label class="block text-sm font-medium text-gray-700">
        <span v-if="value">{{ value }} {{ isOptional ? '(Optional)' : '' }}</span>
        <span v-else>
            <slot />
        </span>
    </label>
</template>
