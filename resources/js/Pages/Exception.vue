<script setup>
import {formatUSD, formatLongDate} from '@/Helpers/format.js';

const props = defineProps({
    session:Object,
    message:[Object, String]
});

</script>

<template>
    <div v-if="message" class="max-w-fit mx-auto py-2 px-4 bg-red-500 rounded-sm text-white font-mono">{{ message }}</div>
</template>