<script setup>
import { useLoader } from "../Composables/loader";

const { isLoading } = useLoader();
</script>

<template>
    <div
        v-if="isLoading"
        class="fixed top-0 left-0 z-50 w-full h-screen translate-x-1/2 translate-y-1/2"
    >
        <div class="flex items-center w-full justfy-center">
            <span class="loader"></span>
        </div>
    </div>
</template>

<style scoped>
.loader {
    width: 64px;
    height: 64px;
    border: 5px solid #fff;
    @apply border-b-primary;
    border-radius: 50%;
    display: inline-block;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}

@keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
