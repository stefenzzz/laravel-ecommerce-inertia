<script setup>
import { cartItems } from '@/Stores/Cart.js'
import { ShoppingCartIcon } from '@heroicons/vue/24/solid';
import CheckoutItem from '@/Components/app/CheckoutItem.vue';
import {computed} from 'vue';
import { formatUSD } from '@/Helpers/format.js';

const cartTotalPrice = computed(()=>{
    return cartItems.data.reduce((acc, e)=> acc += Number(e.quantity) * parseFloat(e.product.price), 0);
 });


</script>

<template>

<aside class="space-y-5 max-w-md w-full mx-auto py-5">
    <h2 class="text-lg font-medium flex gap-x-2 items-center">
        Checkout Items
        <ShoppingCartIcon class="w-6 h-6"/>
    </h2>
    <div class="space-y-6 max-h-[500px] w-full overflow-auto cart-container pr-4">
     <CheckoutItem v-for="cartItem of cartItems.data" :key="cartItem.id" :item="cartItem" />
    </div>
    <h3 class="text-lg text-right border-t pt-5">Total: {{ formatUSD(cartTotalPrice) }}</h3>
</aside>

</template>