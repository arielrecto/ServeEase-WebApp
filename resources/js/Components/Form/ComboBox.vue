<script setup>
import { ref, computed, watch, onMounted } from "vue";

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    identifier: {
        type: String,
        default: "name",
    },
    valueName: {
        type: String,
        default: "id",
    },
    keyName: {
        type: String,
        default: "id",
    },
    class: String,
    initialValue: {
        type: String,
        default: "",
    },
    isRequired: {
        type: Boolean,
        default: false,
    },
});

const query = ref("");
const selectedItems = ref([]);
const selectedText = ref("");
const toggle = ref(false);
const open = ref(false);
const textInputRef = ref();

const resetQuery = () => {
    query.value = "";
};

// Watch for external resets and initialize
watch(
    () => props.initialValue,
    (newValue) => {
        if (newValue) {
            const item = props.items.find(
                (item) => item[props.valueName] === newValue
            );
            if (item) {
                selectedText.value = item[props.identifier];
                selectedItems.value = [{ ...item }];
                if (textInputRef.value) {
                    textInputRef.value.value = item[props.identifier];
                }
            }
        } else {
            selectedText.value = "";
            resetQuery();
            selectedItems.value = [];
            if (textInputRef.value) {
                textInputRef.value.value = "";
            }
        }
    },
    { immediate: true }
);

const emits = defineEmits(["update:model-value", "reset-value"]);

const filteredItems = computed(() => {
    if (!query.value) {
        return props.items;
    }
    return props.items.filter((item) =>
        item[props.identifier].toLowerCase().includes(query.value.toLowerCase())
    );
});

const onInput = (e) => {
    selectedText.value = "";
    query.value = e.target.value;
    textInputRef.value.value = query.value;
};

const onToggle = () => {
    resetQuery();
    toggle.value = !toggle.value;
};

const onClickOutside = () => {
    open.value = false;
    toggle.value = false;
};

const onSelection = (item) => {
    selectedItems.value = [{ ...item }];
    selectedText.value = item.name;
    if (textInputRef.value) {
        textInputRef.value.value = item.name;
    }
    toggle.value = false;
    resetQuery();
    emits("update:model-value", item.id);
};

const vClickOutside = {
    mounted: (el, binding) => {
        const clickOutsideHandler = (event) => {
            if (
                !el.contains(event.target) &&
                typeof binding.value === "function"
            ) {
                binding.value();
            }
        };

        document.addEventListener("click", clickOutsideHandler);
        el.__clickOutsideHandler__ = clickOutsideHandler;
    },
    unmounted: (el) => {
        document.removeEventListener("click", el.__clickOutsideHandler__);
        delete el.__clickOutsideHandler__;
    },
};

// Open/close filtered items list on change
watch([query, toggle], () => {
    if (toggle.value || query.value) open.value = true;
    else open.value = false;
});

// Reset value when input text doesn't match any item
watch(selectedText, () => {
    if (
        selectedText.value &&
        !props.items.find(
            (item) => item[props.identifier] === selectedText.value
        )
    ) {
        emits("reset-value");
    }
});
</script>

<template>
    <div
        v-click-outside="onClickOutside"
        class="relative inline-flex flex-col border border-gray-300 rounded-lg"
        :class="class"
    >
        <div class="flex w-full">
            <input
                @click="toggle = true"
                ref="textInputRef"
                @input="onInput"
                type="text"
                :value="selectedText || query"
                class="w-full m-0 overflow-hidden border-none focus:border-transparent focus:ring-0 focus:outline-none"
                :required="isRequired"
            />
            <button
                @click="onToggle"
                type="button"
                class="inline-flex items-center px-4 border-l border-l-gray-300"
            >
                <i
                    class="fa-solid"
                    :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"
                ></i>
            </button>
        </div>

        <div
            v-if="open"
            class="absolute w-full overflow-y-auto border border-gray-300 rounded-b-lg bg-inherit max-h-60 top-10"
        >
            <div
                @click="onSelection(item)"
                v-for="item in filteredItems"
                :key="item[keyName]"
                class="hover:bg-gray-200 hover:cursor-pointer px-4 py-1.5"
            >
                {{ item.name }}
            </div>
        </div>
    </div>
</template>
