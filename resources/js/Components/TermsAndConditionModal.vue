<script setup>
import { ref, onMounted } from 'vue';
import { Modal } from '@inertiaui/modal-vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['agree', 'close']);
const modalRef = ref(null);
const contentRef = ref(null);
const canAgree = ref(false);

const handleScroll = (e) => {
    const element = e.target;
    const bottom = element.scrollHeight - element.scrollTop - element.clientHeight;

    // Enable button when user scrolls near bottom (within 50px)
    if (bottom < 50) {
        canAgree.value = true;
    }
};

const agree = () => {
    emit('agree', true);
    emit('close');
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black opacity-25"></div>

            <!-- Modal -->
            <div ref="modalRef" class="relative w-full max-w-2xl bg-white rounded-lg shadow-xl">
                <div class="p-6 space-y-6">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Terms and Conditions
                    </h2>

                    <div ref="contentRef" @scroll="handleScroll"
                        class="px-4 overflow-y-auto prose-sm prose max-w-none h-96">
                        <!-- Terms content -->
                        <h3>1. Introduction</h3>
                        <p>Welcome to ServEase. By using our service, you agree to these terms...</p>

                        <h3>2. User Responsibilities</h3>
                        <p>Users must provide accurate information and maintain security...</p>

                        <h3>3. Service Provider Guidelines</h3>
                        <p>Service providers must maintain professional standards...</p>

                        <h3>4. Booking and Payments</h3>
                        <p>All transactions must be processed through our platform...</p>

                        <h3>5. Privacy Policy</h3>
                        <p>We protect your personal information according to law...</p>

                        <h3>6. Dispute Resolution</h3>
                        <p>In case of disputes between users and service providers...</p>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button @click="agree" :disabled="!canAgree"
                            class="button-primary disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ canAgree ? 'I Agree' : 'Please read the terms' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.prose h3 {
    @apply font-semibold text-lg mt-4 mb-2;
}

.prose p {
    @apply mb-4 text-gray-600;
}
</style>
