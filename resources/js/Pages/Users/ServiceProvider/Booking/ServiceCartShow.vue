<script setup>
import { Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

const props = defineProps({
    serviceCart: Object,
    availServices: Array
});

const form = useForm({
    content: '',
    remarkable_id: props.serviceCart?.id,
    remarkable_type: 'ServiceCart'
});

const hasPendingServices = computed(() => {
    return props.availServices.some(service => service.status === 'pending');
});

const approveAll = () => {
    if (confirm('Are you sure you want to approve all services in this cart?')) {
        router.post(route('service-provider.booking.cart.approve-all', props.serviceCart.id));
    }
};

const rejectAll = () => {
    if (confirm('Are you sure you want to reject all services in this cart?')) {
        router.post(route('service-provider.booking.cart.reject-all', props.serviceCart.id));
    }
};

const submitReply = () => {
    form.post(route('service-provider.booking.reply'), {
        preserveScroll: true,
        onSuccess: () => form.reset()
    });
};
</script>

<template>
    <Head title="Service Cart Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('service-provider.booking.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Service Cart - {{ serviceCart.reference_number }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white rounded-lg shadow-xl">
                    <!-- Cart Summary -->
                    <div class="p-6 border-b">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">Reference Number</h3>
                                <p class="text-2xl font-bold text-primary">{{ serviceCart.reference_number }}</p>
                            </div>
                            <div class="text-right">
                                <h3 class="text-lg font-semibold">Total Amount</h3>
                                <p class="text-2xl font-bold text-primary">₱{{ serviceCart.total_amount }}</p>
                            </div>
                        </div>

                        <!-- Service Cart Remarks -->
                        <div v-if="serviceCart.remarks?.length" class="mt-4">
                            <h3 class="mb-2 text-lg font-semibold">Cart Remarks</h3>
                            <div class="p-4 rounded-lg bg-gray-50">
                                <div v-for="remark in serviceCart.remarks" :key="remark.id" class="mb-4 last:mb-0">
                                    <p class="text-gray-700">{{ remark.content }}</p>
                                    <p class="text-sm text-gray-500">By: {{ remark.user?.name }} - {{ new Date(remark.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Reply Form -->
                        <div class="mt-4">
                            <h3 class="mb-2 text-lg font-semibold">Reply to Customer</h3>
                            <form @submit.prevent="submitReply" class="space-y-3">
                                <textarea
                                    v-model="form.content"
                                    rows="3"
                                    class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                    placeholder="Type your reply here..."
                                ></textarea>
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="px-4 py-2 text-white transition-colors rounded-lg bg-primary hover:bg-primary/90 disabled:opacity-50"
                                        :disabled="form.processing || !form.content"
                                    >
                                        Send Reply
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Bulk Actions -->
                        <div v-if="hasPendingServices" class="flex justify-end gap-4 mt-4">
                            <button
                                @click="approveAll"
                                class="px-4 py-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700"
                            >
                                Approve All
                            </button>
                            <button
                                @click="rejectAll"
                                class="px-4 py-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700"
                            >
                                Reject All
                            </button>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-semibold">Services</h3>
                        <div class="space-y-4">
                            <div v-for="service in availServices" :key="service.id"
                                class="p-4 border rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="font-semibold">{{ service.name }}</h4>
                                        <p class="text-sm text-gray-600">₱{{ service.total_price }}</p>

                                        <!-- Service Remarks -->
                                        <div v-if="service.availServiceRemarks?.length" class="mt-3">
                                            <h5 class="mb-1 text-sm font-medium text-gray-700">Service Remarks:</h5>
                                            <div class="p-3 rounded bg-gray-50">
                                                <div v-for="remark in service.availServiceRemarks" :key="remark.id" class="mb-2">
                                                    <p class="text-sm text-gray-700">{{ remark.content }}</p>
                                                    <p class="text-xs text-gray-500">
                                                        By: {{ remark.user?.name }} - {{ new Date(remark.created_at).toLocaleDateString() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <StatusBadge :status="service.status" />
                                </div>
                                <div class="mt-4 text-sm text-gray-600">
                                    <p>Start Date: {{ service.start_date }}</p>
                                    <p>End Date: {{ service.end_date }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
