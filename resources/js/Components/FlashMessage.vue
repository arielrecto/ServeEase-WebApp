<script setup>
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    type: {
        validator(value, props) {
            // The value must match one of these strings
            return ['success', 'info', 'error', 'warning'].includes(value);
        }
    },
    message: {
        type: String,
        default: 'Hello World!'
    }
});

const shown = ref(true);

const icon = computed(() => {
    switch (props.type) {
        case 'success':
            return 'fa-solid fa-circle-check';
        break;
        case 'error':
            return 'fa-solid fa-circle-exclamation';
        break;
        case 'warning':
            return 'fa-solid fa-triangle-exclamation';
        break;
        case 'info':
            return 'fa-solid fa-circle-info';
        break;
    }
});

const color = computed(() => {
    switch (props.type) {
        case 'success':
            return 'alert-success';
        break;
        case 'error':
            return 'alert-error';
        break;
        case 'warning':
            return 'alert-warning';
        break;
        case 'info':
            return 'alert-info';
        break;
    }
});

onMounted(() => {
    setTimeout(() => {
        shown.value = false;
    }, 4000);
});
</script>

<template>
    <div class="fixed left-0 z-40 flex justify-center w-full top-10">
        <div v-if="shown" role="alert" :class="`w-72 alert text-center ${color}`">
            <i :class="icon"></i>
            <span>{{ message }}</span>
        </div>
    </div>
</template>

