<script setup>
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

const props = defineProps({
    serviceCart: Object,
    availServices: Array
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
</script>

<template>
    <Head title="Service Cart Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Service Cart - {{ serviceCart.reference_number }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                    <!-- Cart Summary -->
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold">Reference Number</h3>
                                <p class="text-2xl font-bold text-primary">{{ serviceCart.reference_number }}</p>
                            </div>
                            <div class="text-right">
                                <h3 class="text-lg font-semibold">Total Amount</h3>
                                <p class="text-2xl font-bold text-primary">₱{{ serviceCart.total_amount }}</p>
                            </div>
                        </div>

                        <!-- Bulk Actions -->
                        <div v-if="hasPendingServices" class="flex justify-end gap-4 mt-4">
                            <button
                                @click="approveAll"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            >
                                Approve All
                            </button>
                            <button
                                @click="rejectAll"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                            >
                                Reject All
                            </button>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Services</h3>
                        <div class="space-y-4">
                            <div v-for="service in availServices" :key="service.id"
                                class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold">{{ service.name }}</h4>
                                        <p class="text-sm text-gray-600">₱{{ service.total_price }}</p>
                                        <p class="text-sm text-gray-500 mt-2">{{ service.remarks }}</p>
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
