<template>
    <Head title="Messages" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Messages
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="space-y-2">
                            <div
                                v-for="conversation in conversations"
                                :key="conversation.id"
                                @click="navigateToConversation(conversation.id)"
                                class="flex items-center p-4 transition-all duration-200 ease-in-out border rounded-lg cursor-pointer hover:bg-gray-50"
                            >
                                <!-- User Avatar -->
                                <div class="flex-shrink-0 mr-4">
                                    <div
                                        class="flex items-center justify-center w-12 h-12 text-lg font-semibold text-white rounded-full bg-primary"
                                    >
                                        {{
                                            getInitials(
                                                getOtherUser(conversation).name
                                            )
                                        }}
                                    </div>
                                </div>

                                <!-- Message Content -->
                                <div class="flex-grow min-w-0">
                                    <div
                                        class="flex items-baseline justify-between"
                                    >
                                        <h3
                                            class="font-semibold text-gray-900 truncate"
                                        >
                                            {{
                                                getOtherUser(conversation).name
                                            }}
                                        </h3>
                                        <span
                                            v-if="conversation.messages.length"
                                            class="text-xs text-gray-500"
                                        >
                                            {{
                                                formatTimestamp(
                                                    conversation.messages[0]
                                                        .created_at
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <p
                                        v-if="conversation.messages.length"
                                        class="text-sm text-gray-600 truncate"
                                    >
                                        {{ conversation.messages[0].content }}
                                    </p>
                                </div>

                                <!-- Unread Indicator -->
                                <div
                                    v-if="hasUnseenMessages(conversation)"
                                    class="flex-shrink-0 ml-4"
                                >
                                    <div
                                        class="w-3 h-3 rounded-full bg-primary"
                                    ></div>
                                </div>
                            </div>
                            <p
                                v-if="conversations.length === 0"
                                class="text-center"
                            >
                                There are no conversations.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import moment from "moment";

const props = defineProps({
    conversations: Array,
});

const conversations = ref(props.conversations);

const navigateToConversation = (id) => {
    router.visit(`/messages/${id}`);
};

const getOtherUser = (conversation) => {
    return conversation.owner_id === usePage().props.auth.user.id
        ? conversation.participant
        : conversation.owner;
};

const hasUnseenMessages = (conversation) => {
    return conversation.messages.some(
        (message) =>
            message.receiver_id === usePage().props.auth.user.id &&
            !message.is_seen
    );
};

const getInitials = (name) => {
    return name
        .split(" ")
        .map((word) => word[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
};

const formatTimestamp = (timestamp) => {
    const messageDate = moment(timestamp);
    const now = moment();

    if (messageDate.isSame(now, "day")) {
        return messageDate.format("h:mm A");
    } else if (messageDate.isSame(now, "week")) {
        return messageDate.format("ddd");
    } else {
        return messageDate.format("MMM D");
    }
};
</script>

<style scoped>
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
