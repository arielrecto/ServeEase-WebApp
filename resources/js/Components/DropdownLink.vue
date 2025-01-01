<script setup>
import ModalLinkDialog from "./Modal/ModalLinkDialog.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    href: {
        type: String,
        required: true,
    },
    isModalLink: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Link
        v-if="!isModalLink"
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
    <ModalLinkDialog
        v-else
        :href="href"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
    >
        <slot />
    </ModalLinkDialog>
</template>
