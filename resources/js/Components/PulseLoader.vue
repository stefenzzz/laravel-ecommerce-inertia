<script setup>
import  PulseLoader from 'vue-spinner/src/PulseLoader.vue';
const model = defineModel();
</script>

<template>
    <div v-if="model" class="fixed top-0 h-screen w-screen z-[99] grid place-content-center bg-transparentWhite">
        <PulseLoader class="absolute top-1/3 left-1/2 -translate-y-1/2 -translate-x-1/2"/>
    </div>
</template>