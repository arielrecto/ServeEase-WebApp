<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
    items: Array,
    identifier: String,
    valueName: String,
    keyName: String,
    class: String,
    isRequired: {
        type: Boolean,
        default: false,
    },
});

const query = ref("");
const selectedItem = ref("");
const selectedValue = ref("");
const selectedItems = ref([]);
const toggle = ref(false);
const open = ref(false);
const textInputRef = ref();

const people = [
    { id: 1, name: "Tom Cook" },
    { id: 2, name: "Wade Cooper" },
    { id: 3, name: "Tanya Fox" },
    { id: 4, name: "Arlene Mccoy" },
    { id: 5, name: "Devon Webb" },
    { id: 6, name: "Devon Webb" },
    { id: 7, name: "Devon Webb" },
    { id: 8, name: "Devon Webb" },
    { id: 9, name: "Devon Webb" },
    { id: 10, name: "Devon Webb" },
];

const emits = defineEmits(["update:model-value"]);

const filteredItems = computed(() => {
    if (!query.value) {
        return props.items;
    }
    return props.items.filter((item) =>
        item[props.identifier].toLowerCase().includes(query.value.toLowerCase())
    );
});

const onInput = (e) => {
    selectedItem.value = "";
    query.value = e.target.value;
    textInputRef.value.value = query.value;
    // console.log(query.value);
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
    selectedItems.value.splice(0, selectedItems.value.length);
    selectedItems.value.push({ ...item });
    textInputRef.value.value = item.name;
    selectedItem.value = item.name;
    selectedValue.value = item[props.valueName];
    toggle.value = false;
    resetQuery();
    emits("update:model-value", selectedValue.value);
};

const resetQuery = () => {
    query.value = "";
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

watch([query, toggle], () => {
    if (toggle.value || query.value) open.value = true;
    else open.value = false;
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
                @input="
                    (e) => {
                        onInput(e);
                    }
                "
                type="text"
                :value="selectedItem || query"
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
