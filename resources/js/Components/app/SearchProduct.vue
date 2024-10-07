<script setup>
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { ref, onMounted} from 'vue';
import { router } from '@inertiajs/vue3';

// Uses
let params = ''

// Refs
const search = ref('');

// Methods
function onSearch() {
    if(search.value){
        params.set('search', search.value);
    }else{
        params.delete('search');
    }
    router.get(route('home') + '?' + params.toString());
}

// Hooks
onMounted(() => {
    params = new URLSearchParams(window.location.search)
    search.value = params.get('search') ?? '';
    
});
</script>

<template>
    <section class="max-w-[1280px] flex justify-center -mt-5 mx-auto px-4">
        <div class="bg-white flex gap-x-2 max-w-xl w-full rounded-md border-0 px-2 py-[5px] shadow-sm ring-1 ring-inset ring-gray-300">
            <MagnifyingGlassIcon class="w-6 h-6 text-gray-500"/>
            <input type="search" class="w-full text-black placeholder:text-gray-500
            text-sm md:text-[14px] font-poppins font-light tracking-wide outline-none"
            v-model="search"
            autocomplete
            @keyup.enter.prevent="onSearch"
            placeholder="Search Product"
            />
        </div>   
    </section> 
</template>