<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    icon: String,
    url: {
        type: String,
        default: "#",
    },
    title: "String",
});
</script>

<template>
    <div class="w-32 space-y-2 text-center">
        <div
            class="w-20 h-20 mx-auto transition-all ease-out bg-white rounded-full shadow group hover:bg-primary hover:shadow-lg hover:scale-105 active:scale-90"
        >
            <Link
                :href="url"
                class="flex items-center justify-center w-full h-full text-2xl text-gray-500 group-hover:text-white"
            >
                <i :class="icon"></i>
            </Link>
        </div>
        <div class="font-bold">{{ title }}</div>
    </div>
</template>
