<script setup>
import { ref, onMounted, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";

const props = defineProps({
    provider: Object,
    services: Array,
    initialService: Object
});

const selectedServices = ref([]);

const form = ref({
    services: [],
    start_date: '',
    end_date: '',
    remark: '',
    total_amount : '',
    serviceDetails: {} // Store details for each service (bargain price and remarks)
});

// Initialize serviceDetails with default values
onMounted(() => {
    props.services.forEach(service => {
        form.value.serviceDetails[service.id] = {
            is_bargain: false,
            bargain_price: service.price,
            remark: ''
        };
    });

    const currentUrl = window.location.href;
    const urlParams = new URL(currentUrl);
    const serviceId = urlParams.searchParams.get('query[service_id]');
    if (serviceId) {
        selectedServices.value = [parseInt(serviceId)];
        form.value.services = [parseInt(serviceId)];
    }
});

const submit = () => {
    router.post(route('customer.services.bulk-avail'), {
        services: selectedServices.value,
        start_date: form.value.start_date,
        end_date: form.value.end_date,
        remark: form.value.remark,
        total_amount : form.value.total_amount,
        serviceDetails: form.value.serviceDetails
    });
};

// Add computed property for total price
form.value.total_amount = computed(() => {
    return selectedServices.value.reduce((total, serviceId) => {
        const serviceDetail = form.value.serviceDetails[serviceId];
        return total + (serviceDetail ? serviceDetail.bargain_price : 0);
    }, 0);
});

// Function to enable bargain price editing
const enableBargain = (serviceId) => {
    const serviceDetail = form.value.serviceDetails[serviceId];
    serviceDetail.is_bargain = true;
    serviceDetail.remark = `I would like to bargain the price to ₱${serviceDetail.bargain_price}.`;
};
</script>

<template>
    <Head title="Bulk Service Availing" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton :url="route('customer.services.show', initialService.id)" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Bulk Service Booking - {{ provider.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white rounded-lg shadow-xl">
                    <form @submit.prevent="submit" class="p-8">
                        <!-- Services Selection -->
                        <div class="mb-8">
                            <h3 class="mb-6 text-xl font-semibold">Available Services</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div v-for="service in services" :key="service.id"
                                    class="p-6 transition-shadow duration-200 border rounded-xl hover:shadow-md">
                                    <label class="flex items-start gap-4 cursor-pointer">
                                        <input type="checkbox" :value="service.id" v-model="selectedServices"
                                            class="w-5 h-5 mt-1 border-gray-300 rounded text-primary focus:ring-primary" />
                                        <div class="flex-1">
                                            <h4 class="mb-2 text-lg font-semibold">{{ service.name }}</h4>
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="px-3 py-1 text-sm rounded-full bg-primary/10 text-primary">
                                                    ₱{{ service.price }} / {{ service.price_type }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600">
                                                {{ service.description }}
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div class="p-6 mb-8 bg-gray-50 rounded-xl">
                            <h3 class="mb-6 text-xl font-semibold">Booking Details</h3>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Start Date</label>
                                    <input type="date" v-model="form.start_date"
                                        class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                        required />
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">End Date</label>
                                    <input type="date" v-model="form.end_date"
                                        class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                        required />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Special
                                        Instructions</label>
                                    <textarea v-model="form.remark"
                                        class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                        rows="4" required
                                        placeholder="Add any special instructions or notes for the service provider"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="p-6 mb-8 bg-gray-50 rounded-xl">
                            <h3 class="mb-4 text-xl font-semibold">Order Summary</h3>
                            <div class="space-y-4">
                                <div v-for="serviceId in selectedServices" :key="serviceId"
                                    class="flex flex-col py-2 border-b">
                                    <div class="flex justify-between items-center">
                                        <span>{{ services.find(s => s.id === serviceId)?.name }}</span>
                                        <span>
                                            <input
                                                v-if="form.serviceDetails[serviceId]?.is_bargain"
                                                type="number"
                                                v-model="form.serviceDetails[serviceId].bargain_price"
                                                class="w-24 border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                                @input="form.serviceDetails[serviceId].remark = `I would like to bargain the price to ₱${form.serviceDetails[serviceId].bargain_price}.`"
                                            />
                                            <span v-else>
                                                ₱{{ form.serviceDetails[serviceId]?.bargain_price }}
                                            </span>
                                            <button
                                                v-if="!form.serviceDetails[serviceId]?.is_bargain"
                                                type="button"
                                                @click="enableBargain(serviceId)"
                                                class="ml-2 text-sm text-blue-500 hover:underline"
                                            >
                                                Bargain Price
                                            </button>
                                        </span>
                                    </div>
                                    <textarea
                                        v-if="form.serviceDetails[serviceId]?.is_bargain"
                                        v-model="form.serviceDetails[serviceId].remark"
                                        class="w-full mt-2 border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                                        rows="2"
                                        placeholder="Add a remark for this service"
                                    ></textarea>
                                </div>
                                <div class="flex justify-between pt-4 text-lg font-bold">
                                    <span>Total Amount</span>
                                    <span class="text-primary">₱{{ form.total_amount }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-8 py-3 font-medium text-white transition-colors duration-200 rounded-lg bg-primary hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="selectedServices.length === 0">
                                Proceed to Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
