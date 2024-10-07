<script setup>
defineProps({
    length:[Number, String],
    limit: [Number, String],
})

const emits = defineEmits(['onToggle']);
</script>


<template>
        <span v-if="length === limit" @click="emits('onToggle')" class="text-blue-500 hover:text-blue-600 cursor-pointer">
                ..See More
        </span>
        <span v-else-if="length > limit" @click="emits('onToggle')" class="text-blue-500 hover:text-blue-600 cursor-pointer">
                See Less
        </span>
</template>