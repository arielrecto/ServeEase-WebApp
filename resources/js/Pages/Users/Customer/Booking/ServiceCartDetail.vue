<script setup>
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StatusBadge from "@/Components/StatusBadge.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";

const props = defineProps({
    serviceCart: Object,
    availServices: Array,
});

const form = useForm({
    content: '',
    remarkable_id: props.serviceCart?.id,
    remarkable_type: 'ServiceCart'
});

const submitReply = () => {
    form.post(route('customer.booking.reply'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Service Cart Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('customer.booking.index')" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Service Cart Details - {{ serviceCart.reference_number }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <!-- Cart Info -->
                    <div class="p-6 border-b">
                        <div class="flex justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold">Reference Number</h3>
                                <p class="text-2xl font-bold text-primary">{{ serviceCart.reference_number }}</p>
                            </div>
                            <div class="text-right">
                                <h3 class="text-lg font-semibold">Total Amount</h3>
                                <p class="text-2xl font-bold text-primary">₱{{ serviceCart.total_amount }}</p>
                            </div>
                        </div>

                        <!-- Cart Remarks -->
                        <div v-if="serviceCart.remarks?.length" class="mt-6">
                            <h3 class="text-lg font-semibold mb-4">Cart Remarks</h3>
                            <div class="space-y-4">
                                <div v-for="remark in serviceCart.remarks" :key="remark.id"
                                    class="p-4 bg-gray-50 rounded-lg border">
                                    <p class="text-gray-700">{{ remark.content }}</p>
                                    <p class="text-sm text-gray-500 mt-2">
                                        By: {{ remark.user?.name }} - {{ new Date(remark.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-6">Services</h3>
                        <div class="space-y-6">
                            <div v-for="service in availServices" :key="service.id"
                                class="p-4 border rounded-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium">{{ service.name }}</h4>
                                        <p class="text-gray-600">₱{{ service.total_price }}</p>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-600">Start Date: {{ service.start_date }}</p>
                                            <p class="text-sm text-gray-600">End Date: {{ service.end_date }}</p>
                                        </div>
                                    </div>
                                    <StatusBadge :status="service.status" />
                                </div>

                                <!-- Service Remarks -->
                                <div v-if="service.availServiceRemarks?.length" class="mt-4">
                                    <h5 class="font-medium text-gray-700 mb-2">Service Remarks</h5>
                                    <div class="space-y-3">
                                        <div v-for="remark in service.availServiceRemarks" :key="remark.id"
                                            class="p-3 bg-gray-50 rounded-lg border">
                                            <p class="text-sm">{{ remark.content }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                By: {{ remark.user?.name }} - {{ new Date(remark.created_at).toLocaleDateString() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reply Form -->
                    <div class="p-6 border-t bg-gray-50">
                        <h3 class="font-semibold mb-3">Add Reply</h3>
                        <form @submit.prevent="submitReply">
                            <textarea
                                v-model="form.content"
                                rows="3"
                                class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                placeholder="Type your reply here..."
                            ></textarea>
                            <div class="flex justify-end mt-3">
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50"
                                    :disabled="form.processing || !form.content"
                                >
                                    Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
