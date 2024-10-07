<script setup>
import {PlusIcon, MinusIcon} from '@heroicons/vue/24/solid';
const model = defineModel();
defineProps({
    max: {
        type:[Number, String],
        default:100
    },
    min: {
        type:[Number, String],
        default:1
    }
});
</script>

<template>
    <div class="flex items-center gap-x-2">
        <PlusIcon class="w-6 h-6 text-black bg-gray-200 px-1 cursor-pointer active:bg-gray-300" @click="model < max && ++model"/>
        <MinusIcon class="w-6 h-6 text-black bg-gray-200 px-1 cursor-pointer active:bg-gray-300" @click="model > min && --model"/>
    </div>
</template>