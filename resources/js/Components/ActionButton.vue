<script setup>
import { computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import ModalLinkDialog from '@/Components/Modal/ModalLinkDialog.vue';
import ModalLinkSlideover from '@/Components/Modal/ModalLinkSlideover.vue';

const props = defineProps({
    type: {
        validator(value, props) {
            // The value must match one of these strings
            return ['link', 'modal', 'button'].includes(value);
        }
    },
    href: String,
    actionType: String,
    color: String,
    modalSlideoverEnabled: {
        type: [Boolean, null],
        default: null,
    }
});

const tooltipText = computed(() => {
    const firstChar = props.actionType[0].toUpperCase();

    return `${firstChar}${props.actionType.slice(1)}`;
});

const icon = computed(() => {
    switch (props.actionType.toLowerCase()) {
        case 'view':
            return 'ri-eye-line';
        break;
        case 'edit':
            return 'fa-solid fa-pen-to-square';
        break;
        case 'delete':
            return 'ri-delete-bin-line';
        break;
        case 'approve':
            return 'fa-solid fa-thumbs-up';
        break;
        case 'reject':
            return 'fa-solid fa-thumbs-down';
        break;
        default:
        break;
    }
});

const CLASSES = 'inline-flex items-center justify-center w-12 h-12 text-lg transition duration-200 ease-in-out border border-gray-300 rounded-lg bg-neutral-50 active:scale-95';
</script>

<template>
    <div class="tooltip" :data-tip="tooltipText">
        <template v-if="type === 'link'">
            <Link :href="href" :class="CLASSES">
                <i :class="icon"></i>
            </Link>
        </template>
        <template v-else-if="type === 'modal'">
            <template v-if="!modalSlideoverEnabled">
                <ModalLinkDialog :href="href" :class="CLASSES">
                    <i :class="icon"></i>
                </ModalLinkDialog>
                <!-- <ModalLink :href="href" panel-classes="bg-white space-y-6 px-6 py-4 overflow-hidden rounded-md shadow" :class="CLASSES">
                    <i :class="icon"></i>
                </ModalLink> -->
            </template>
            <template v-else>
                <!-- <ModalLink :href="href" slideover position="right" panel-classes="bg-white min-h-screen space-y-10 px-6 py-4 overflow-y-auto space-y-6 rounded-md shadow" :class="CLASSES">
                    <i :class="icon"></i>
                </ModalLink> -->
                <ModalLinkSlideover :href="href" :class="CLASSES">
                    <i :class="icon"></i>
                </ModalLinkSlideover>
            </template>
        </template>
        <template v-else-if="type === 'button'">
            <button></button>
        </template>
    </div>
</template>
