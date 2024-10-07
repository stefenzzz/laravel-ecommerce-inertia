<script setup>
import { StarIcon as StarSolidIcon } from '@heroicons/vue/24/solid';

defineProps({
   rate: Number,
   starClass: {
    type: String,
    default: 'w-5 h-5 text-yellow-500'
   },
   gap:{
    type: String,
    default: 'gap-x-2'
   }
}); 

</script>

<template>
    <div :class="['flex items-center', gap]">
        <StarSolidIcon v-for="i in rate" :key="i" :class="starClass"/>
    </div>
</template>