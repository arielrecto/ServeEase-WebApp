<script setup>
defineProps({
    isResponsive: {
        type: Boolean,
        default: true,
    }
});
</script>

<template>
    <div :class="isResponsive ? 'overflow-x-auto' : ''">
        <table class="table table-zebra">
            <thead v-if="$slots.header">
                <slot name="header" />
            </thead>
            <tbody v-if="$slots.body">
                <slot name="body" />
            </tbody>
        </table>
    </div>
</template>
