<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";

const props = defineProps({
    availService: Object,
    paymentAccounts: Object, // Changed to Object since it's grouped
    service: Object,
    transactionData: Object,
});

const selectedAccount = ref(null);
const selectedAccountType = ref(null);

// Group payment methods by type for better organization
const groupedPaymentMethods = computed(() => {
    return Object.entries(props.paymentAccounts).map(([type, accounts]) => ({
        type: type,
        name:
            type === "gcash"
                ? "GCash"
                : type === "paymaya"
                ? "PayMaya"
                : type === "cash"
                ? "Cash"
                : "Bank Account",
        icon:
            type === "gcash"
                ? "fa-wallet text-blue-500"
                : type === "paymaya"
                ? "fa-mobile-screen-button text-purple-500"
                : type === "cash"
                ? "fa-money-bill text-green-500"
                : "fa-building-columns text-gray-700",
        accounts: accounts,
    }));
});

// Add computed for minimum payment amount
const reservationFee = computed(() => props.availService.total_price * 0.1); // Fixed ₱500 reservation fee

const minimumPayment = computed(() => {
    if (form.transaction_type === "reservation") {
        return reservationFee.value;
    }
    return form.transaction_type === "deposit"
        ? Math.ceil(props.availService.total_price * 0.3) // 30% minimum deposit
        : props.availService.total_price;
});

// Add computed for remaining balance
const remainingBalance = computed(() => {
    return props.availService.total_price - form.amount;
});

// Add computed for checking if selected payment is cash
const isCashPayment = computed(() => {
    return selectedAccount.value?.account_type === "cash";
});

// Modified form with amount validation
const form = useForm({
    payment_account_id: "",
    transaction_type: "payment",
    amount: props.availService?.total_price || 0,
    currency: "PHP",
    reference_number: "",
    transactionable_id: props.availService?.id,
    transactionable_type: "AvailService",
    paid_by: null,
    paid_to: props.service?.user?.id,
    status: "pending",
    attachments: [],
});

// Add a watch effect to update form when props are loaded
watch(
    () => props.service,
    (newService) => {
        if (newService?.user?.id) {
            form.paid_to = newService.user.id;
        }
    },
    { immediate: true }
);

watch(
    () => props.availService,
    (newAvailService) => {
        if (newAvailService) {
            form.amount = newAvailService.total_price;
            form.transactionable_id = newAvailService.id;
        }
    },
    { immediate: true }
);

const transactionTypes = [
    {
        name: "Full Payment",
        value: "payment",
        description: "Complete payment for the service",
        icon: "fa-solid fa-circle-check",
    },
    {
        name: "Partial Payment",
        value: "deposit",
        description: "Initial deposit or partial payment",
        icon: "fa-solid fa-clock",
    },
    {
        name: "Reservation Fee",
        value: "reservation",
        description: "Pay reservation fee to secure booking at 10%",
        icon: "fa-solid fa-calendar-check",
    },
];

const handleFileUpload = (event) => {
    form.attachments = Array.from(event.target.files);
};

// Add amount validation
const validateAmount = (value) => {
    const amount = parseFloat(value);
    if (isNaN(amount) || amount <= 0) {
        form.setError("amount", "Please enter a valid amount");
        return false;
    }

    if (form.transaction_type === "reservation") {
        if (amount !== reservationFee.value) {
            form.setError(
                "amount",
                `Reservation fee must be exactly ₱${reservationFee.value}`
            );
            return false;
        }
        return true;
    }

    if (amount < minimumPayment.value) {
        form.setError(
            "amount",
            `Minimum payment required is ₱${minimumPayment.value.toLocaleString()}`
        );
        return false;
    }
    if (amount > props.availService.total_price) {
        form.setError("amount", "Amount cannot exceed the total price");
        return false;
    }
    return true;
};

// Add form validation before submit
const validateForm = () => {
    if (!validateAmount(form.amount)) return false;

    if (!selectedAccount.value) {
        form.setError("payment_account_id", "Please select a payment method");
        return false;
    }

    // Only validate reference number and attachments if not cash payment
    if (!isCashPayment.value) {
        if (!form.reference_number) {
            form.setError("reference_number", "Reference number is required");
            return false;
        }

        if (!form.attachments.length) {
            form.setError("attachments", "Please upload proof of payment");
            return false;
        }
    }

    return true;
};

const submit = () => {
    if (!validateForm()) return;

    form.post(route("customer.booking.pay"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            // Show success message
        },
        onError: () => {
            // Show error message
        },
    });
};

// Add a watch to set the amount when transaction type changes
watch(
    () => form.transaction_type,
    (newType) => {
        if (newType === "reservation") {
            form.amount = reservationFee.value;
        } else if (newType === "payment") {
            form.amount = props.availService.total_price;
        } else {
            form.amount = minimumPayment.value;
        }
    }
);
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <HeaderBackButton />
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Payment for {{ service.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Service Provider Details -->
                <div
                    class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="flex items-center gap-x-4">
                            <img
                                :src="
                                    transactionData.service_provider
                                        .profile_photo
                                "
                                class="w-16 h-16 rounded-full"
                                alt="Provider Photo"
                            />
                            <div>
                                <h3 class="text-lg font-medium">
                                    {{ transactionData.service_provider.name }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Service Provider
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Service Summary -->
                        <div class="p-4 mb-6 rounded-lg bg-gray-50">
                            <h3 class="mb-2 text-lg font-medium">
                                Transaction Details
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <p class="text-gray-600">Service</p>
                                    <p class="font-medium">
                                        {{ service.name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Amount to Pay</p>
                                    <p class="text-xl font-bold text-primary">
                                        ₱{{ form.amount.toLocaleString() }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Provider</p>
                                    <p class="font-medium">
                                        {{ service.user?.name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Transaction Type -->
                            <div class="space-y-3">
                                <InputLabel value="Select Payment Type" />
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                >
                                    <div
                                        v-for="type in transactionTypes"
                                        :key="type.value"
                                        @click="
                                            form.transaction_type = type.value
                                        "
                                        :class="[
                                            'p-4 border rounded-lg cursor-pointer transition-all',
                                            form.transaction_type === type.value
                                                ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                                : 'hover:border-gray-300',
                                            (availService.status !== 'confirmed' && type.value === 'reservation') && 'sr-only'
                                        ]"
                                    >
                                        <div class="flex items-start gap-x-3">
                                            <div
                                                :class="[
                                                    'mt-1 p-2 rounded-lg',
                                                    form.transaction_type ===
                                                    type.value
                                                        ? 'bg-primary/10'
                                                        : 'bg-gray-100',
                                                ]"
                                            >
                                                <i
                                                    :class="[
                                                        type.icon,
                                                        'text-lg',
                                                        form.transaction_type ===
                                                        type.value
                                                            ? 'text-primary'
                                                            : 'text-gray-600',
                                                    ]"
                                                ></i>
                                            </div>
                                            <div>
                                                <h4 class="font-medium">
                                                    {{ type.name }}
                                                </h4>
                                                <p
                                                    class="text-sm text-gray-600"
                                                >
                                                    {{ type.description }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError
                                    :message="form.errors.transaction_type"
                                />
                            </div>

                            <!-- Add this after the Transaction Type selection and before Payment Methods -->
                            <div class="space-y-3">
                                <InputLabel value="Payment Amount" />
                                <div
                                    class="grid grid-cols-1 gap-6 md:grid-cols-2"
                                >
                                    <div class="space-y-2">
                                        <TextInput
                                            v-model="form.amount"
                                            type="number"
                                            class="block w-full"
                                            :min="minimumPayment"
                                            :max="availService.total_price"
                                            step="0.01"
                                            required
                                            @input="
                                                validateAmount(
                                                    $event.target.value
                                                )
                                            "
                                        />
                                        <InputError
                                            :message="form.errors.amount"
                                        />
                                        <div
                                            class="flex flex-col text-sm text-gray-600"
                                        >
                                            <span
                                                >Total Price: ₱{{
                                                    availService.total_price.toLocaleString()
                                                }}</span
                                            >
                                            <span
                                                v-if="
                                                    form.transaction_type ===
                                                    'deposit'
                                                "
                                                :class="{
                                                    'text-primary':
                                                        remainingBalance > 0,
                                                }"
                                            >
                                                Remaining Balance: ₱{{
                                                    remainingBalance.toLocaleString()
                                                }}
                                            </span>
                                            <span
                                                v-if="
                                                    form.transaction_type ===
                                                    'deposit'
                                                "
                                                class="text-xs"
                                            >
                                                Minimum Deposit Required: ₱{{
                                                    minimumPayment.toLocaleString()
                                                }}
                                                (30%)
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-4 rounded-lg bg-gray-50">
                                        <h4 class="mb-2 font-medium">
                                            Payment Summary
                                        </h4>
                                        <div class="space-y-2 text-sm">
                                            <div class="flex justify-between">
                                                <span>Service Total:</span>
                                                <span
                                                    >₱{{
                                                        availService.total_price.toLocaleString()
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                class="flex justify-between font-medium text-primary"
                                            >
                                                <span>{{
                                                    form.transaction_type ===
                                                    "deposit"
                                                        ? "Deposit Amount:"
                                                        : "Payment Amount:"
                                                }}</span>
                                                <span
                                                    >₱{{
                                                        form.amount.toLocaleString()
                                                    }}</span
                                                >
                                            </div>
                                            <div
                                                v-if="
                                                    form.transaction_type ===
                                                    'deposit'
                                                "
                                                class="flex justify-between pt-2 border-t"
                                            >
                                                <span
                                                    >Balance After
                                                    Payment:</span
                                                >
                                                <span
                                                    >₱{{
                                                        remainingBalance.toLocaleString()
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="space-y-6">
                                <h3
                                    class="flex items-center text-lg font-medium gap-x-2"
                                >
                                    <i
                                        class="fa-solid fa-credit-card text-primary"
                                    ></i>
                                    Select Payment Method
                                </h3>

                                <!-- Empty State Message -->
                                <div
                                    v-if="
                                        !Object.keys(groupedPaymentMethods)
                                            .length
                                    "
                                    class="p-8 text-center border border-gray-300 border-dashed rounded-lg bg-gray-50"
                                >
                                    <div
                                        class="flex flex-col items-center gap-y-3"
                                    >
                                        <i
                                            class="text-4xl text-gray-400 fa-solid fa-wallet"
                                        ></i>
                                        <div class="space-y-1">
                                            <h4
                                                class="font-medium text-gray-900"
                                            >
                                                No Payment Methods Available
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                The service provider hasn't
                                                added any payment methods yet.
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Please contact the provider to
                                                set up payment methods.
                                            </p>
                                        </div>
                                        <!-- Message Provider Button -->
                                        <Link
                                            :href="
                                                route('messages.index', {
                                                    participant_id:
                                                        service.user.id,
                                                })
                                            "
                                            class="inline-flex items-center px-4 py-2 mt-2 text-sm font-medium transition-colors rounded-lg text-primary bg-primary/10 hover:bg-primary/20"
                                        >
                                            <i
                                                class="mr-2 fa-solid fa-message"
                                            ></i>
                                            Message Provider
                                        </Link>
                                    </div>
                                </div>

                                <!-- Payment Methods List -->
                                <template v-else>
                                    <div
                                        v-for="group in groupedPaymentMethods"
                                        :key="group.type"
                                        class="space-y-4"
                                    >
                                        <h3
                                            class="flex items-center text-lg font-medium gap-x-2"
                                        >
                                            <i
                                                :class="[
                                                    'fa-solid',
                                                    group.icon,
                                                ]"
                                            ></i>
                                            {{ group.name }}
                                        </h3>
                                        <div
                                            class="grid grid-cols-1 gap-4 md:grid-cols-2"
                                        >
                                            <div
                                                v-for="account in group.accounts"
                                                :key="account.id"
                                                @click="
                                                    selectedAccount = account;
                                                    form.payment_account_id =
                                                        account.id;
                                                "
                                                :class="[
                                                    'p-4 border rounded-lg cursor-pointer transition-all relative',
                                                    selectedAccount?.id ===
                                                    account.id
                                                        ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                                        : 'hover:border-gray-300',
                                                ]"
                                            >
                                                <!-- Selected Indicator -->
                                                <div
                                                    v-if="
                                                        selectedAccount?.id ===
                                                        account.id
                                                    "
                                                    class="absolute flex items-center justify-center w-6 h-6 text-white rounded-full -top-2 -right-2 bg-primary"
                                                >
                                                    <i
                                                        class="text-xs fa-solid fa-check"
                                                    ></i>
                                                </div>

                                                <!-- Account Info -->
                                                <div class="space-y-2">
                                                    <div
                                                        class="flex items-center justify-between"
                                                    >
                                                        <p class="font-medium">
                                                            {{
                                                                account.account_name
                                                            }}
                                                        </p>
                                                        <span
                                                            v-if="
                                                                account.is_primary
                                                            "
                                                            class="px-2 py-0.5 text-xs bg-primary/10 text-primary rounded-full"
                                                        >
                                                            Primary
                                                        </span>
                                                    </div>
                                                    <p
                                                        class="font-mono text-sm text-gray-600"
                                                    >
                                                        {{
                                                            account.account_number
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <InputError
                                    :message="form.errors.payment_account_id"
                                />
                            </div>

                            <!-- Reference Number -->
                            <div v-if="!isCashPayment">
                                <InputLabel
                                    for="reference_number"
                                    value="Reference Number"
                                />
                                <TextInput
                                    id="reference_number"
                                    v-model="form.reference_number"
                                    type="text"
                                    class="block w-full mt-1"
                                    placeholder="Enter the transaction reference number"
                                    required
                                />
                                <p class="mt-1 text-sm text-gray-500">
                                    Enter the reference number from your payment
                                    transaction
                                </p>
                                <InputError
                                    :message="form.errors.reference_number"
                                />
                            </div>

                            <!-- Currency -->
                            <div>
                                <InputLabel for="currency" value="Currency" />
                                <TextInput
                                    id="currency"
                                    v-model="form.currency"
                                    type="text"
                                    class="block w-full mt-1"
                                    readonly
                                />
                            </div>

                            <!-- Proof of Payment Upload -->
                            <div>
                                <InputLabel
                                    for="attachments"
                                    value="Proof of Payment"
                                />
                                <input
                                    type="file"
                                    id="attachments"
                                    @input="handleFileUpload"
                                    accept="image/*,.pdf"
                                    multiple
                                    class="block w-full px-3 py-2 mt-1 text-sm border border-gray-300 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
                                    required
                                />
                                <p class="mt-1 text-sm text-gray-500">
                                    Upload screenshots or PDF of your payment
                                    receipt (Max 5MB per file)
                                </p>
                                <InputError
                                    :message="form.errors.attachments"
                                />
                            </div>

                            <!-- Enhanced Submit Button Section -->
                            <div
                                class="flex items-center justify-between pt-6 border-t"
                            >
                                <div class="text-sm text-gray-600">
                                    <p class="flex items-center gap-x-1">
                                        <i
                                            class="fa-solid fa-shield-check text-primary"
                                        ></i>
                                        Secure Payment Process
                                    </p>
                                </div>
                                <PrimaryButton
                                    :disabled="
                                        form.processing || !selectedAccount
                                    "
                                    class="px-6 py-3"
                                >
                                    <i class="mr-2 fa-solid fa-lock"></i>
                                    {{
                                        form.processing
                                            ? "Processing..."
                                            : "Complete Payment"
                                    }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
