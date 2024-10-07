<script setup>
import { TransitionChild, TransitionRoot} from '@headlessui/vue';
import { XCircleIcon } from '@heroicons/vue/24/solid';
import { ref } from 'vue';
const model = defineModel();
const props = defineProps({
    product: Object,
});
const emits = defineEmits(['update:modelValue']);
const imageRef = ref();

const closeModal = (ev)=>{
    if(ev.target === imageRef.value) return;    
    emits('update:modelValue', false);
}


</script>
<template>

    <TransitionRoot as="section" :show="model" class="fixed inset-0 z-50">
        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                      leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-black bg-opacity-80 transition-opacity" ></div> 
        </TransitionChild>
      
        <TransitionChild
            @click="closeModal($event)"
            as="div"
            class="absolute h-screen w-screen inset-0 px-5"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <XCircleIcon class="absolute top-5 right-2 md:right-10 w-10 h-10 text-gray-200 cursor-pointer z-10"/>
            <figure class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[900px] max-h-[900px]">
                <img :src="product.image" :alt="product.title" class="object-contain mx-auto" ref="imageRef">  
            </figure>        
        </TransitionChild>

    </TransitionRoot>
</template>