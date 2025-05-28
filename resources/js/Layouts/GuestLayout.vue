<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import FlashMessage from "@/Components/FlashMessage.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <FlashMessage
        v-if="$page.props.flash.message_success"
        type="success"
        :message="$page.props.flash.message_success"
    />
    <FlashMessage
        v-if="$page.props.flash.message_error"
        type="error"
        :message="$page.props.flash.message_error"
    />
    <FlashMessage
        v-if="$page.props.flash.message_warning"
        type="warning"
        :message="$page.props.flash.message_warning"
    />

    <div
        class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <ApplicationLogo size="lg" class="text-gray-500 fill-current" />
            </Link>
        </div>

        <div
            class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
