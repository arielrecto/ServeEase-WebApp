<script setup>
import { watch } from "vue";

const props = defineProps({
    placeholder: {
        default: "Select an option",
        type: String,
    },
    choices: {
        type: [Array, Object],
        required: false,
    },
    optionKey: {
        type: [String, null],
        default: null,
    },
    valueKey: {
        type: [String, null],
        default: null,
    },
    modelValue: {
        type: [String, Number],
        default: "",
    },
});

const emits = defineEmits(["update:modelValue"]);

// Watch for external resets - when modelValue becomes empty/null
watch(
    () => props.modelValue,
    (newValue) => {
        if (!newValue && newValue !== 0) {
            emits("update:modelValue", "");
        }
    }
);
</script>

<template>
    <select
        :value="modelValue"
        @change="emits('update:modelValue', $event.target.value)"
        class="text-gray-900 border-gray-300 focus:border-primary focus:ring-primary rounded-xl"
    >
        <option value="" disabled selected>{{ placeholder }}</option>
        <template v-if="choices">
            <option
                v-for="choice in choices"
                :value="valueKey ? choice[valueKey] : choice"
            >
                {{ optionKey ? choice[optionKey] : choice }}
            </option>
        </template>

        <!-- Additional options -->
        <slot />
    </select>
</template>
