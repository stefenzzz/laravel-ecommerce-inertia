<script setup>
import {ref} from 'vue';
import {EyeIcon} from '@heroicons/vue/24/outline';
import {EyeIcon as EyeIconSolid} from '@heroicons/vue/24/solid';
const model = defineModel();
defineProps({
    name: String,
    placeholder: String,
    required: Boolean,
    message:String,
});
const isPasswordVisible = ref(false);


</script>

<template>
    <div>
        <div class="relative custom-input flex items-center pr-3">      
            <input v-bind:type="isPasswordVisible ? 'text' : 'password'" v-model="model" :name="name" :placeholder="placeholder" class="block w-full outline-none pr-2">
            <span v-if="!isPasswordVisible"  @click="isPasswordVisible = !isPasswordVisible" class="cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#4B5563" width="20" height="20" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-.06.196-.125.39-.195.584"/>
                </svg>
            </span>
            <EyeIconSolid v-else @click="isPasswordVisible = !isPasswordVisible" class="w-6 h-5 text-gray-600 cursor-pointer"/>
        </div>
        <span v-if="message" class="text-sm text-red-500">{{ message }}</span>
    </div>
</template>