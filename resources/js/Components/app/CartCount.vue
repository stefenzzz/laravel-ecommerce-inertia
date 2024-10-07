<script setup>

const count = defineModel();

</script>

<template>
    <small v-if="count" class=" py-[1px] px-[8px] rounded-full bg-red-500 text-white">{{ count }}</small>
</template>