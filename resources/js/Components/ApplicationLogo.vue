<script setup>
import { computed } from "vue";

const props = defineProps({
    size: {
        type: String,
        default: "md",
    },
});

const logoSize = computed(() => {
    return {
        md: "text-lg",
        lg: "text-2xl",
    }[props.size];
});
</script>

<template>
    <h1 :class="['font-black uppercase', logoSize]">
        Serv<span class="text-primary">Ease</span>
    </h1>
</template>
