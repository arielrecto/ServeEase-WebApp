<script setup>
defineProps({
    headers: {
        type: Array
    }
});
</script>

<template>
    <tr>
        <th v-for="(item, index) in headers" :key="index">{{ item }}</th>
    </tr>
</template>
