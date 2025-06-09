<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import moment from "moment";

const props = defineProps({
    service: Object,
});

const form = useForm({
    startDate: null,
    endDate: null,
    startTime: null,  // Add this
    endTime: null,    // Add this
    hours: null,
    total: null,
    service: props.service.id,
    remark: null,
    quantity: 1,
    attachments: [],
    includeTime: false, // Add this toggle state
});

const quantity = ref(1);

const incrementQuantity = () => {
    console.log(quantity);
    quantity.value++;
    updateTotal();
};

const decrementQuantity = () => {
    if (quantity.value > 1) {
        quantity.value--;
        updateTotal();
    }
};

const updateTotal = () => {
    if (props.service.price_type === "hr" && form.hours) {
        form.total = props.service.price * form.hours * quantity.value;
    } else if (props.service.price_type === "fixed rate") {
        form.total = props.service.price * quantity.value;
    } else if (form.startDate && form.endDate) {
        const days = moment(form.endDate).diff(form.startDate, "days");
        form.total = props.service.price * (days || 1) * quantity.value;
    }
};

watch(
    () => quantity.value,
    () => {
        updateTotal;
        form.quantity = quantity.value;
    }
);
watch(
    () => form.startDate,
    (value) => {
        if (value) {
            form.endDate = value;
            form.total = props.service.price;
        }
        console.log(props.service.price_type);
    }
);
watch(
    () => form.endDate,
    (value) => {
        if (value) {
            if (props.service.price_type === "hr") return;
            if (moment(form.endDate).diff(form.startDate, "days") <= 0) {
                form.total = props.service.price;
                return;
            }
            form.total =
                props.service.price *
                moment(form.endDate).diff(form.startDate, "days");
        }
    }
);
watch(
    () => form.hours,
    (value) => {
        if (value) {
            form.total = props.service.price * value;
        }
    }
);
watch(() => form.hours, updateTotal);
watch(() => form.endDate, updateTotal);

const submit = () => {
    form.post(route("customer.services.avail.store"), {
        onFinish: () => form.reset(),
    });
};

// Add these functions
const handleFileUpload = (e) => {
    const files = Array.from(e.target.files);
    form.attachments = [...form.attachments, ...files];
};

const removeFile = (index) => {
    form.attachments.splice(index, 1);
};

const getFileIcon = (file) => {
    if (file.type.startsWith("image/")) return "fa-image";
    if (file.type.includes("pdf")) return "fa-file-pdf";
    return "fa-file";
};

const back = () => {
    window.history.back();
};

// Add computed property for time validation
const minTime = computed(() => {
    if (!form.startDate || form.startDate !== moment().format('YYYY-MM-DD')) {
        return null;
    }
    return moment().format('HH:mm');
});

// Add time validation watcher
watch(() => form.startTime, (value) => {
    if (value && form.endTime && value >= form.endTime) {
        form.endTime = moment(value, 'HH:mm')
            .add(1, 'hour')
            .format('HH:mm');
    }
});
</script>

<template>
    <Head :title="`Avail ${service.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-x-4">
                <button @click="back" class="btn btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Avail Service
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                    <!-- Service Header -->
                    <div class="relative h-48 bg-gray-900">
                        <img
                            :src="service.service_thumbnail"
                            class="object-cover w-full h-full opacity-75"
                            alt="Service thumbnail"
                        />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"
                        ></div>
                        <div class="absolute bottom-0 left-0 p-6 text-white">
                            <h1 class="text-3xl font-bold capitalize">
                                {{ service.name }}
                            </h1>
                            <div class="flex items-center mt-2 gap-x-4">
                                <div class="flex items-center">
                                    <i class="mr-2 fas fa-user-circle"></i>
                                    {{ service.user.name }}
                                </div>
                                <div class="flex items-center">
                                    <i class="mr-2 fas fa-tag"></i>
                                    ₱{{ service.price }} /
                                    {{ service.price_type }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 lg:p-8">
                        <form
                            @submit.prevent="submit"
                            class="flex flex-col gap-2"
                        >
                            <!-- Scheduling Section -->
                            <div class="space-y-6">
                                <div class="p-4 border rounded-lg bg-gray-50">
                                    <h3
                                        class="flex items-center mb-4 text-lg font-medium"
                                    >
                                        <i
                                            class="mr-2 fas fa-calendar text-primary"
                                        ></i>
                                        Schedule Details
                                    </h3>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel
                                                for="startDate"
                                                value="Start Date"
                                            />
                                            <TextInput
                                                id="startDate"
                                                type="date"
                                                :min="
                                                    new Date().toLocaleDateString(
                                                        'en-CA'
                                                    )
                                                "
                                                v-model="form.startDate"
                                                class="block w-full mt-1"
                                                required
                                            />
                                            <InputError
                                                :message="form.errors.startDate"
                                            />
                                        </div>

                                        <div
                                            :class="[
                                                service.price_type ===
                                                    'fixed rate' && 'sr-only',
                                            ]"
                                        >
                                            <InputLabel
                                                :value="
                                                    service.price_type === 'hr'
                                                        ? 'Hours'
                                                        : 'End Date'
                                                "
                                            />
                                            <TextInput
                                                v-if="
                                                    service.price_type !== 'hr'
                                                "
                                                type="date"
                                                :min="form.startDate"
                                                v-model="form.endDate"
                                                class="block w-full mt-1"
                                                :disabled="
                                                    service.price_type ===
                                                    'fixed rate'
                                                "
                                                required
                                            />
                                            <TextInput
                                                v-else
                                                type="number"
                                                v-model="form.hours"
                                                class="block w-full mt-1"
                                                min="1"
                                                required
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.endDate ||
                                                    form.errors.hours
                                                "
                                            />
                                        </div>
                                    </div>

                                    <!-- Inside the Schedule Details section, after date inputs -->
                                    <div class="mt-4">
                                        <!-- <div class="flex items-center mb-4">
                                            <input
                                                type="checkbox"
                                                id="includeTime"
                                                v-model="form.includeTime"
                                                class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                                            >
                                            <label for="includeTime" class="ml-2 text-sm text-gray-600">
                                                Specify time for the service
                                            </label>
                                        </div> -->

                                        <div  class="grid grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel for="startTime" value="Start Time" />
                                                <TextInput
                                                    id="startTime"
                                                    type="time"
                                                    v-model="form.startTime"
                                                    class="block w-full mt-1"
                                                    required
                                                />
                                                <InputError :message="form.errors.startTime" />
                                            </div>

                                            <div>
                                                <InputLabel for="endTime" value="End Time" />
                                                <TextInput
                                                    id="endTime"
                                                    type="time"
                                                    v-model="form.endTime"
                                                    class="block w-full mt-1"
                                                    required
                                                />
                                                <InputError :message="form.errors.endTime" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quantity Section -->
                                <div
                                    v-if="service.is_quantifiable"
                                    class="p-4 border rounded-lg bg-gray-50"
                                >
                                    <h3
                                        class="flex items-center mb-4 text-lg font-medium"
                                    >
                                        <i
                                            class="mr-2 fas fa-cubes text-primary"
                                        ></i>
                                        Quantity
                                    </h3>

                                    <div
                                        class="flex items-center justify-center gap-x-6"
                                    >
                                        <button
                                            type="button"
                                            @click="decrementQuantity"
                                            class="flex items-center justify-center w-12 h-12 transition-colors bg-gray-100 rounded-full hover:bg-gray-200"
                                            :disabled="quantity <= 1"
                                        >
                                            <i
                                                class="text-gray-600 fas fa-minus"
                                            ></i>
                                        </button>

                                        <div
                                            class="flex flex-col items-center min-w-[80px]"
                                        >
                                            <span
                                                class="text-2xl font-medium"
                                                >{{ quantity }}</span
                                            >
                                            <!-- <span class="text-sm text-gray-500">
                                                Available: {{ service.quantity }}
                                            </span> -->
                                        </div>

                                        <button
                                            type="button"
                                            @click="incrementQuantity"
                                            class="flex items-center justify-center w-12 h-12 transition-colors bg-gray-100 rounded-full hover:bg-gray-200"
                                        >
                                            <i
                                                class="text-gray-600 fas fa-plus"
                                            ></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <InputLabel value="Total Amount" />
                                <div class="relative mt-1">
                                    <div
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                                    >
                                        <span class="text-gray-500">₱</span>
                                    </div>
                                    <TextInput
                                        v-model="form.total"
                                        type="number"
                                        class="block w-full pl-8"
                                        readonly
                                    />
                                </div>
                                <p
                                    v-if="service.is_quantifiable"
                                    class="mt-1 text-sm text-gray-500"
                                >
                                    ₱{{ (form.total / quantity).toFixed(2) }} ×
                                    {{ quantity }}
                                    {{ quantity > 1 ? "units" : "unit" }}
                                </p>
                            </div>

                            <!-- Add Remark Field -->
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <h3
                                    class="flex items-center mb-4 text-lg font-medium"
                                >
                                    <i
                                        class="mr-2 fas fa-comment-alt text-primary"
                                    ></i>
                                    Additional Notes (Optional)
                                </h3>

                                <div>
                                    <textarea
                                        v-model="form.remark"
                                        rows="4"
                                        class="w-full border-gray-300 rounded-lg focus:border-primary focus:ring-primary"
                                        placeholder="Add any special instructions or requirements..."
                                    ></textarea>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Optional: Include any specific
                                        requirements or notes for the service
                                        provider
                                    </p>
                                    <InputError
                                        :message="form.errors.remark"
                                        class="mt-1"
                                    />
                                </div>
                            </div>

                            <!-- Add this before the submit button -->
                            <div class="p-4 border rounded-lg bg-gray-50">
                                <h3
                                    class="flex items-center mb-4 text-lg font-medium"
                                >
                                    <i
                                        class="mr-2 fas fa-paperclip text-primary"
                                    ></i>
                                    Attachments (Optional)
                                </h3>

                                <div class="space-y-4">
                                    <div
                                        class="flex items-center justify-center w-full"
                                    >
                                        <label
                                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100"
                                        >
                                            <div
                                                class="flex flex-col items-center justify-center pt-5 pb-6"
                                            >
                                                <i
                                                    class="mb-2 text-2xl text-gray-400 fas fa-cloud-upload-alt"
                                                ></i>
                                                <p
                                                    class="mb-2 text-sm text-gray-500"
                                                >
                                                    <span class="font-semibold"
                                                        >Click to upload</span
                                                    >
                                                    or drag and drop
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    Any file type (MAX. 10MB)
                                                </p>
                                            </div>
                                            <input
                                                type="file"
                                                class="hidden"
                                                multiple
                                                @change="handleFileUpload"
                                            />
                                        </label>
                                    </div>

                                    <!-- File Preview -->
                                    <div
                                        v-if="form.attachments.length"
                                        class="grid grid-cols-2 gap-4"
                                    >
                                        <div
                                            v-for="(
                                                file, index
                                            ) in form.attachments"
                                            :key="index"
                                            class="relative flex items-center p-2 bg-white border rounded-lg"
                                        >
                                            <i
                                                :class="[
                                                    'fas',
                                                    getFileIcon(file),
                                                    'text-gray-400 mr-3',
                                                ]"
                                            ></i>
                                            <div class="flex-1 min-w-0">
                                                <p
                                                    class="text-sm font-medium text-gray-900 truncate"
                                                >
                                                    {{ file.name }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        (
                                                            file.size /
                                                            1024 /
                                                            1024
                                                        ).toFixed(2)
                                                    }}
                                                    MB
                                                </p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="removeFile(index)"
                                                class="ml-2 text-gray-400 hover:text-red-500"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <InputError
                                        :message="form.errors.attachments"
                                        class="mt-2"
                                    />
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        class="flex items-center px-6 py-3 text-white transition-colors rounded-lg bg-primary hover:bg-primary/90 gap-x-2"
                                        :disabled="form.processing"
                                    >
                                        <i class="fas fa-check"></i>
                                        {{
                                            form.processing
                                                ? "Processing..."
                                                : "Confirm Booking"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
