<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed, getCurrentInstance } from "vue";
import moment from "moment";

import ModalLinkDialog from "@/Components/Modal/ModalLinkDialog.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderBackButton from "@/Components/HeaderBackButton.vue";
import FeedbackList from "@/Components/Feedbacks/FeedbackList.vue";
import StatusBadge from "@/Components/StatusBadge.vue";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

const props = defineProps({
    availService: Object,
    service: Object,
});

const instance = getCurrentInstance();
const state = reactive({
    availServiceBtnShown: false,
    tabs: [
        { name: "About", value: "0" },
        { name: "Payment History", value: "1" },
        { name: "Reviews", value: "2" },
    ],
});

const back = () => {
    window.history.back();
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-700',
        completed: 'bg-green-100 text-green-700',
        failed: 'bg-red-100 text-red-700',
        partial: 'bg-blue-100 text-blue-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
};

// Add these computed properties
const totalPaid = computed(() => {
    if (!props.availService?.transactions?.length) return 0;

    return props.availService.transactions
        .filter(tx => tx.status === 'approved')
        .reduce((sum, tx) => sum + tx.amount, 0);
});

const remainingBalance = computed(() => {
    const total = props.availService?.total_price || 0;
    return total - totalPaid.value;
});

const paymentStatus = computed(() => {
    if (remainingBalance.value <= 0) return 'paid';
    return totalPaid.value > 0 ? 'partial' : 'pending';
});
</script>

<template>

    <Head :title="service.name" />

    <AuthenticatedLayout>
        <template #header>
            <button @click="back" type="button" class="button-ghost">
                <i class="fi fi-br-arrow-left"></i>
            </button>
        </template>
        <div class="py-12">
            <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="h-[35vh] w-full">
                        <img :src="service.service_thumbnail ??
                            'https://images.unsplash.com/photo-1631451095765-2c91616fc9e6?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80'
                            " alt="Image showing the service" class="object-cover w-full h-full" />
                    </div>

                    <div class="p-8">
                        <Tabs value="0">
                            <TabList>
                                <Tab v-for="tab in state.tabs" :value="tab.value" :key="tab.value">{{ tab.name }}</Tab>
                            </TabList>
                            <TabPanels>
                                <TabPanel value="0">
                                    <div class="flex flex-col gap-y-4 md:grid md:grid-cols-3 gap-x-8">
                                        <div class="order-2 col-span-2 md:order-1">
                                            <div class="mb-12 space-y-2">
                                                <h1 class="text-2xl font-bold">
                                                    {{ service.name }}
                                                </h1>
                                                <span class="inline-block mr-3 text-sm">
                                                    <i class="text-yellow-500 fa-solid fa-star"></i>
                                                    {{ service.avg_rate }}
                                                </span>
                                            </div>

                                            <div class="flex flex-col gap-y-1">
                                                <span class="text-gray-600">Description</span>
                                                <div class="overflow-y-auto max-h-48">
                                                    <span class="">{{
                                                        service.description
                                                        }}</span>
                                                </div>
                                            </div>
                                            <div v-if="
                                                service.terms_and_conditions
                                            " class="flex flex-col gap-y-1">
                                                <span class="text-gray-600">Terms & conditions</span>
                                                <div class="overflow-y-auto max-h-48">
                                                    <span class="">{{
                                                        service.terms_and_conditions
                                                        }}</span>
                                                </div>
                                            </div>
                                            <div class="flex flex-col mt-20 gap-y-3">
                                                <span class="text-xl font-semibold">About the provider</span>
                                                <div class="flex flex-col gap-y-1">
                                                    <div class="flex items-start gap-x-4">
                                                        <div
                                                            class="w-16 h-16 overflow-hidden bg-gray-600 rounded-full aspect-square">
                                                            <img :src="service.user
                                                                .profile
                                                                .user_avatar
                                                                " class="object-cover w-full h-full" />
                                                        </div>
                                                        <div class="flex flex-col space-y-1">
                                                            <span class="text-xl">{{
                                                                service.user
                                                                    .name
                                                            }}</span>
                                                            <span class="text-sm italic text-gray-600">{{
                                                                service.user
                                                                    .profile
                                                                    .provider_profile
                                                                    .contact
                                                            }}</span>
                                                            <span class="text-sm italic text-gray-600">Experience:
                                                                {{
                                                                    service.user
                                                                        .profile
                                                                        .provider_profile
                                                                        .experience
                                                                }}</span>
                                                            <Link :href="route(
                                                                'profile.showProviderProfile',
                                                                service
                                                                    .user
                                                                    .profile
                                                                    .provider_profile
                                                                    .id
                                                            )
                                                                " class="underline text-primary">Go to
                                                            Profile</Link>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="order-1 h-auto p-5 space-y-4 border border-gray-300 rounded-lg md:order-2">
                                            <div class="flex gap-x-12">
                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        Agreed Price
                                                    </div>
                                                    <div class="font-bold">
                                                        ₱
                                                        <span class="text-xl text-primary">{{
                                                            availService.total_price.toLocaleString()
                                                        }}</span>
                                                    </div>
                                                </div>
                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        Status
                                                    </div>
                                                    <StatusBadge :status="availService.status
                                                        " />
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <template v-if="
                                                    availService.service
                                                        .price_type === 'hr'
                                                ">
                                                    <div class="text-gray-600">
                                                        Total Work Hours
                                                    </div>
                                                    <div class="font-bold">
                                                        <span class="text-xl text-primary">{{
                                                            availService.total_hours.toLocaleString()
                                                        }}</span>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="text-gray-600">
                                                        Total Work Days
                                                    </div>
                                                    <div class="font-bold">
                                                        <span class="text-xl text-primary">{{
                                                            moment(
                                                                availService.end_date
                                                            ).diff(
                                                                moment(
                                                                    availService.start_date
                                                                ),
                                                                "days"
                                                            )
                                                        }}</span>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="space-y-1">
                                                <div class="text-gray-600">
                                                    Service Price * rate
                                                </div>
                                                <div class="font-bold">
                                                    ₱<span class="text-xl text-primary">{{
                                                        availService.service.price.toLocaleString()
                                                    }}
                                                        *
                                                    </span>

                                                    <span class="text-xl text-primary">{{
                                                        availService.service.price_type.toLocaleString()
                                                    }}</span>
                                                </div>
                                            </div>
                                            <div class="flex gap-x-12">
                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        Start Date
                                                    </div>
                                                    <div class="font-bold">
                                                        {{
                                                            moment(
                                                                availService.start_date
                                                            ).format(
                                                                "MMM DD, YYYY"
                                                            )
                                                        }}
                                                    </div>
                                                </div>

                                                <div class="space-y-1">
                                                    <div class="text-gray-600">
                                                        End Date
                                                    </div>
                                                    <div class="font-bold">
                                                        {{
                                                            moment(
                                                                availService.end_date
                                                            ).format(
                                                                "MMM DD, YYYY"
                                                            )
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="text-gray-600">
                                                    Remarks
                                                </div>
                                                <div>
                                                    {{ availService.remarks }}
                                                </div>
                                            </div>

                                            <div>
                                                <ModalLinkDialog v-if="
                                                    availService.status ===
                                                    'completed' &&
                                                    !availService.has_feedback
                                                " :href="route(
                                                    'customer.feedbacks.create'
                                                ) +
                                                    `?id=${availService.id}`
                                                    " class="w-full btn button-ghost">
                                                    <i class="ri-edit-2-line"></i>
                                                    Write a review
                                                </ModalLinkDialog>
                                            </div>

                                            <div v-if="availService.status === 'completed'" class="relative">
                                                <div v-if="state.availServiceBtnShown"
                                                    @click="state.availServiceBtnShown = false"
                                                    class="fixed inset-0 z-40">
                                                </div>
                                                <div v-if="state.availServiceBtnShown"
                                                    class="absolute z-50 flex flex-col w-full gap-2 p-2 bg-white border rounded-md bottom-10">
                                                    <Link
                                                        :href="route('customer.services.bulk-form', { provider_id: service.user.id, query: { service_id: service.id } })"
                                                        class="btn btn-secondary">
                                                    Add to Bulk Service
                                                    </Link>
                                                    <!--<Link :href="route(
                                                        'customer.services.avail.create',
                                                        service.id
                                                    )
                                                        " class="btn btn-primary">Avail</Link>-->
                                                </div>
                                                <button
                                                    @click="state.availServiceBtnShown = !state.availServiceBtnShown"
                                                    type="button"
                                                    class="flex items-center justify-center w-full text-center button-ghost">
                                                    <i class="ri-shopping-cart-line"></i>
                                                    Avail again
                                                </button>
                                            </div>

                                            <Link :href="route('messages.index', {
                                                participant_id:
                                                    service.user.id,
                                            })
                                                "
                                                class="flex items-center justify-center w-full gap-2 py-2 text-sm text-white uppercase rounded-lg bg-primary hover:bg-primary-dark">
                                            <i class="ri-message-3-line"></i>
                                            Message Provider
                                            </Link>

                                            <Link v-if="availService.status === 'completed'"
                                                :href="route('customer.booking.payment', availService.id)"
                                                class="flex items-center justify-center w-full gap-2 py-2 text-sm text-white uppercase rounded-lg bg-primary hover:bg-primary/90">
                                            <i class="fa-solid fa-credit-card"></i>
                                            Make Payment
                                            </Link>
                                        </div>
                                    </div>
                                </TabPanel>

                                <TabPanel value="1">
                                    <div class="space-y-6">
                                        <!-- Payment Progress Section -->
                                        <div class="p-4 rounded-lg bg-gray-50">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-medium">Payment Summary</h3>
                                                <!-- <StatusBadge :status="paymentStatus" /> -->
                                            </div>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                                <div>
                                                    <p class="text-sm text-gray-600">Total Amount</p>
                                                    <p class="text-xl font-bold text-gray-900">
                                                        {{ formatCurrency(availService.total_price) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600">Amount Paid</p>
                                                    <p class="text-xl font-bold text-primary">
                                                        {{ formatCurrency(totalPaid) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600">Remaining Balance</p>
                                                    <p class="text-xl font-bold"
                                                        :class="remainingBalance > 0 ? 'text-yellow-600' : 'text-green-600'">
                                                        {{ formatCurrency(remainingBalance) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Transactions List -->
                                        <div class="space-y-4">
                                            <h3 class="text-lg font-medium">Transaction History</h3>

                                            <div v-if="availService.transactions?.length"
                                                class="divide-y divide-gray-200">
                                                <div v-for="transaction in availService.transactions"
                                                    :key="transaction.id"
                                                    class="p-4 transition-colors hover:bg-gray-50">
                                                    <div class="flex items-start justify-between">
                                                        <div class="space-y-1">
                                                            <div class="flex items-center gap-x-2">
                                                                <span class="font-medium capitalize">
                                                                    {{ transaction.transaction_type === 'deposit' ?
                                                                        'Partial Payment' : 'Full Payment' }}
                                                                </span>
                                                                <span :class="[
                                                                    'px-2 py-0.5 text-xs rounded-full',
                                                                    getStatusColor(transaction.status)
                                                                ]">
                                                                    {{ transaction.status }}
                                                                </span>
                                                            </div>
                                                            <p class="text-sm text-gray-600">
                                                                Reference: {{ transaction.reference_number }}
                                                            </p>
                                                            <p class="text-sm text-gray-500">
                                                                Paid via: {{ transaction.payment_account?.account_type
                                                                }}
                                                            </p>
                                                            <p class="text-xs text-gray-500">
                                                                {{
                                                                    moment(transaction.created_at).format('MMM DD, YYYY h:mm A')
                                                                }}
                                                            </p>
                                                        </div>
                                                        <div class="text-right">
                                                            <p class="text-lg font-medium text-primary">
                                                                {{ formatCurrency(transaction.amount) }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Proof of Payment -->
                                                    <div v-if="transaction.attachments?.length"
                                                        class="pt-3 mt-3 border-t">
                                                        <p class="mb-2 text-xs text-gray-600">Attachments:</p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <a v-for="attachment in transaction.attachments"
                                                                :key="attachment.id"
                                                                :href="'/storage/' + attachment.file_path"
                                                                target="_blank"
                                                                class="inline-flex items-center px-2 py-1 text-xs transition-colors bg-gray-100 rounded gap-x-1 hover:bg-gray-200">
                                                                <i class="text-gray-500 fa-solid fa-file-image"></i>
                                                                {{ attachment.file_name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Empty State -->
                                            <div v-else class="py-12 text-center rounded-lg bg-gray-50">
                                                <i class="mb-3 text-4xl text-gray-400 fa-solid fa-receipt"></i>
                                                <h4 class="font-medium text-gray-900">No Payment Records</h4>
                                                <p class="mt-1 text-sm text-gray-600">
                                                    No payments have been made for this service yet.
                                                </p>
                                                <Link v-if="availService.status === 'pending'"
                                                    :href="route('customer.booking.payment', availService.id)"
                                                    class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white rounded-lg bg-primary hover:bg-primary/90">
                                                <i class="mr-2 fa-solid fa-credit-card"></i>
                                                Make Payment
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </TabPanel>

                                <TabPanel value="2">
                                    <FeedbackList :service="service" />
                                </TabPanel>
                            </TabPanels>
                        </Tabs>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
