<template>
    <Head :title="`Chat with ${otherUser.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Chat with {{ otherUser.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <Link href="/messages"
                                  class="text-blue-500 hover:underline">
                                Back to Messages
                            </Link>
                        </div>

                        <div ref="messagesContainer"
                             class="space-y-4 h-96 overflow-y-auto mb-4">
                            <div v-for="message in messages"
                                 :key="message.id"
                                 class="flex"
                                 :class="message.sender_id === auth.user.id ? 'justify-end' : 'justify-start'">
                                <div :class="[
                                    message.sender_id === auth.user.id
                                        ? 'bg-blue-500 text-white'
                                        : 'bg-gray-200 text-gray-900',
                                    'rounded-lg px-4 py-2 max-w-md'
                                ]">
                                    <p>{{ message.content }}</p>
                                    <p :class="[
                                        'text-xs',
                                        message.sender_id === auth.user.id
                                            ? 'text-blue-100'
                                            : 'text-gray-500'
                                    ]">
                                        {{ formatDate(message.created_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="otherUserIsTyping" class="text-sm text-gray-500 italic">
                            {{ otherUser.name }} is typing...
                        </div>

                        <form @submit.prevent="sendMessage" class="mt-4">
                            <div class="flex space-x-2">
                                <input v-model="newMessage"
                                       @input="handleTyping"
                                       type="text"
                                       class="flex-1 rounded-lg border-gray-300"
                                       placeholder="Type your message..."
                                       required>
                                <button type="submit"
                                        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                    Send
                                </button>
                            </div>
                        </form>

                        <button @click="soundEnabled = !soundEnabled"
                                class="text-gray-500 hover:text-gray-700"
                                :title="soundEnabled ? 'Mute sound' : 'Unmute sound'">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="h-5 w-5"
                                 :class="{ 'opacity-50': !soundEnabled }"
                                 viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path v-if="soundEnabled" fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd" />
                                <path v-else fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM12.293 7.293a1 1 0 011.414 1.414L10.414 12l3.293 3.293a1 1 0 01-1.414 1.414L9 13.414l-3.293 3.293a1 1 0 01-1.414-1.414L7.586 12 4.293 8.707a1 1 0 011.414-1.414L9 10.586l3.293-3.293z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, nextTick, computed, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import dayjs from 'dayjs'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

const props = defineProps({
    conversation: Object
})

const messages = ref(props.conversation.messages || [])
const newMessage = ref('')
const messagesContainer = ref(null)
const auth = usePage().props.auth

// Echo instance
let echo = null

const otherUser = computed(() => {
    return props.conversation.owner_id === auth.user.id
        ? props.conversation.participant
        : props.conversation.owner
})

const isTyping = ref(false)
const otherUserIsTyping = ref(false)
let typingTimeout = null

const messageSound = new Audio('/message.mp3')
const soundEnabled = ref(true)

onMounted(() => {
    scrollToBottom()
    initializeEcho()
})

onUnmounted(() => {
    if (echo) {
        echo.leave(`conversation.${props.conversation.id}`)
    }
})

const initializeEcho = () => {
    echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: false,
        enabledTransports: ['ws', 'wss'],
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    })

    const channel = echo.private(`conversation.${props.conversation.id}`)

    channel.listen('.message.sent', (e) => {
        if (e.message.sender_id !== auth.user.id) {
            messages.value.push(e.message)
            if (soundEnabled.value) {
                messageSound.play().catch(error => {
                    console.log('Could not play message sound:', error)
                })
            }
            nextTick(() => {
                scrollToBottom()
            })
        }
    })

    channel.listenForWhisper('typing', (e) => {
        if (e.user.id !== auth.user.id) {
            otherUserIsTyping.value = true
            setTimeout(() => {
                otherUserIsTyping.value = false
            }, 3000)
        }
    })

    channel.listenForWhisper('stopTyping', (e) => {
        if (e.user.id !== auth.user.id) {
            otherUserIsTyping.value = false
        }
    })
}

const sendMessage = () => {
    if (!newMessage.value.trim()) return

    // Create a temporary message object
    const messageData = {
        content: newMessage.value,
        sender_id: auth.user.id,
        created_at: new Date().toISOString()
    }

    // Add message to the local array immediately
    messages.value.push(messageData)

    router.post(`/messages/${props.conversation.id}`, {
        content: newMessage.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newMessage.value = ''
            nextTick(() => {
                scrollToBottom()
            })
        }
    })
}

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

const formatDate = (date) => {
    return dayjs(date).format('MMM D, YYYY HH:mm')
}

const handleTyping = () => {
    if (typingTimeout) clearTimeout(typingTimeout)

    echo.private(`conversation.${props.conversation.id}`)
        .whisper('typing', {
            user: auth.user
        })

    typingTimeout = setTimeout(() => {
        echo.private(`conversation.${props.conversation.id}`)
            .whisper('stopTyping', {
                user: auth.user
            })
    }, 1000)
}
</script>
