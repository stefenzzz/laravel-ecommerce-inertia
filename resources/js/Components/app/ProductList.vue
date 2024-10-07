<script setup>
import { formatUSD } from '@/Helpers/format.js';
import { addToCart } from '@/Stores/Cart.js';
import Ratings from '@/Components/Ratings.vue';
import { showCartPanel } from '@/Stores/Cart.js';
import {ref} from 'vue';

defineProps({
product: Object
})
const productRate = ref(5.0);
</script>

<template>
    <article class="w-full px-2 md:px-4">
        
        <Link class="block" :href="route('product.view', product.slug)" @click="showCartPanel = false">      
            <img class="mx-auto h-48  object-cover" :src="product.image" :alt="product.title">
        </Link>

        <div class="mt-4 flex flex-grow flex-col">
            <Link :href="route('product.view', product.slug)">
                <h2 class="overflow-hidden text-ellipsis whitespace-nowrap leading-6 tracking-wide">{{ product.title }}</h2>
            </Link>
            <span class="mt-1 text-[15px] font-semibold">{{ formatUSD(product.price) }}</span>
            <div class="flex items-start gap-x-2 mt-3">
                <Ratings :rate="productRate" :starClass="'w-4 h-4 text-yellow-500'" gap="gap-x-1"/>
                <h3 class="text-sm">({{ productRate.toFixed(1) }})</h3>
            </div>
            <button @click="addToCart(product.id)" class="block mt-5 button-gray rounded-sm w-full">Add to Cart</button>
        </div>

    </article>
</template>