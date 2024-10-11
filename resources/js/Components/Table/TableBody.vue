<script setup>
import {h, ref} from 'vue';

defineProps({
    items: {
        type: Object,
    },
    keyName: {
        type: String,
    },
    isFirstRowHeader: {
        type: Boolean,
        default: true,
    }
});
</script>

<template>
        <tr v-for="(item, index) in items" :key="item[index]">
            <template v-for="(value, key) in item" :key="key">
                <!-- <th>{{  value + ' ' + key }}</th> -->
                <th v-if="isFirstRowHeader && key === keyName">{{ value }}</th>
                <td v-else>{{ value }}</td>
            </template>

            <!-- Add columns if needed -->
            <slot />
        </tr>
</template>
