<script setup>
import { ModalLink } from '@inertiaui/modal-vue';
import { ref } from 'vue';

defineProps({
    href: String,
    position: {
        type: String,
        default: 'center',
    },
    maxWidth: {
        type: String,
        default: 'md',
        validator(value, props) {
            // The value must match one of these strings
            return ['sm', 'md', 'lg', 'xl'].includes(value);
        }
    },
    closeButton: {
        type: Boolean,
        default: true,
    },
    class: String,
});

const modalRef = ref(null);
</script>

<template>
    <ModalLink :href="href" :close-button="closeButton" :position="position" :max-width="maxWidth" panel-classes="inertia-modal" :class="class">
        <slot />
    </ModalLink>
</template>
