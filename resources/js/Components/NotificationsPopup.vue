<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import moment from "moment";
import axios from "axios";

import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

const props = defineProps({
    user: Object,
});

const notifications = ref(usePage().props.auth.notifications);

// Removes excess notification by filtering up to 8 latest ones
const filteredNotifications = computed(() => {
    if (notifications.value.length === 0) {
        return [];
    }

    if (notifications.value.length > 8) {
        return notifications.value.slice(0, 8);
    }

    return notifications.value;
});

const newNotificationReceived = computed(() => {
    return notifications.value.some((notification) => !notification.is_seen);
});

// console.log(typeof filteredNotifications.value);

let echo = null;

const initializeEcho = () => {
    echo = new Echo({
        broadcaster: "reverb",
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: false,
        enabledTransports: ["ws", "wss"],
        auth: {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                "X-Requested-With": "XMLHttpRequest",
            },
        },
    });

    // Using props.user.id seem to doesn't work
    const channel = echo.private(`notifications.${props.user.id}`);

    channel.listen("NotificationSent", (e) => {
        if (e.notification.user_id === props.user.id) {
            const alreadyExists = notifications.value.some(
                (n) => n.id === e.notification.id
            );

            if (!alreadyExists) {
                notifications.value.unshift(e.notification);
            }
        }
    });
};

const handleNewNotifications = async () => {
    if (newNotificationReceived.value) {
        const unreadNotifications = notifications.value.filter(
            (notification) => !notification.is_seen
        );

        unreadNotifications.forEach((notification) => {
            notification.is_seen = true;
        });

        const res = await axios.put("/notifications/read", {
            notifications: unreadNotifications,
        });
    }
};

onMounted(() => {
    initializeEcho();
});

onUnmounted(() => {
    if (echo) {
        echo.leave(`notifications.${props.user.id}`);
    }
});
</script>

<template>
    <div class="relative ms-3">
        <div
            v-if="newNotificationReceived"
            class="absolute top-0 right-0 z-10 bg-blue-600 rounded-full size-3"
        ></div>

        <Dropdown
            align="right"
            @dropdown-open="handleNewNotifications"
            width="80"
            contentClasses="py-1 bg-white max-h-[50vh] overflow-y-auto"
        >
            <template #trigger>
                <span class="inline-flex rounded-md">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center w-10 h-10 text-lg font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-400 rounded-full hover:text-gray-700 focus:outline-none hover:bg-gray-100"
                    >
                        <i class="ri-notification-2-line"></i>
                    </button>
                </span>
            </template>

            <template #content>
                <DropdownLink
                    v-for="notification in filteredNotifications"
                    :href="notification.url"
                >
                    <div class="space-y-1">
                        <div v-html="notification.content"></div>
                        <div class="text-xs">
                            {{ moment(notification.created_at).format("LLL") }}
                        </div>
                    </div>
                </DropdownLink>
                <div
                    v-if="filteredNotifications.length === 0"
                    class="py-5 text-sm text-center"
                >
                    There are no notifications.
                </div>
            </template>
        </Dropdown>
    </div>
</template>
