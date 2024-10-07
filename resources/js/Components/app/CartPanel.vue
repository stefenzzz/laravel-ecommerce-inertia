<script setup>
import { TransitionRoot, TransitionChild } from '@headlessui/vue';
import CartItems from '@/Components/app/CartItems.vue';
import { router } from '@inertiajs/vue3';

const model = defineModel();
const emits = defineEmits(['update:modelValue']);
const closeCartPanel = ()=>{
    emits('update:modelValue', false)
}
// close cart panel when navigating
router.on('start', (event) => {
  closeCartPanel();
});
</script>

<template>

    <TransitionRoot as="section" :show="model" class="relative">
    <TransitionChild 
      as="aside" 
      enter="transition-transform ease-out duration-300"
      enter-from="translate-x-full"
      enter-to="translate-x-0"
      leave="transition-transform ease-in duration-200"
      leave-from="translate-x-0"
      leave-to="translate-x-full"
      class="fixed h-screen inset-y-0 right-0 md:max-w-md w-full border-l flex flex-col bg-white px-4 pt-28"
    >
            
    <CartItems @closePanel="closeCartPanel" :inCartPanel="true" imgW="75" imgH="75"/>
        
    </TransitionChild>
    </TransitionRoot>

</template>