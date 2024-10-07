<script setup>
import { ShoppingCartIcon, ChevronDoubleRightIcon } from '@heroicons/vue/24/solid';
import CartItem from '@/Components/app/CartItem.vue';
import { formatUSD } from '@/Helpers/format.js';
import { cartItems } from '@/Stores/Cart.js';
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {pulseLoading} from '@/Stores/Extra.js';

const page = usePage();
defineProps({
    inCartPanel: {type:Boolean, default:false},
    imgW: String,
    imgH: String,
    imgClass: String,
});
const emits = defineEmits(['closePanel']);

const checkout = ()=>{

    if(page.props.auth.user){
        pulseLoading.value = true;
        router.post( route('checkout') ,{},{
            onFinish:()=> pulseLoading.value = false,
        });
        return;
    }
        router.get(route('checkout.form'));
    


}

const cartTotalPrice = computed(()=>{
    return cartItems.data.reduce((acc, e)=> acc += Number(e.quantity) * parseFloat(e.product.price), 0);
 });

</script>

<template>

        <!-- cart items container -->
        <div class="space-y-6 overflow-y-auto cart-container max-h-full" :class="{'px-4':inCartPanel }">

            <div class="flex items-center gap-x-1">                
                <ChevronDoubleRightIcon v-if="inCartPanel" class="w-6 h-6 cursor-pointer" @click="emits('closePanel')" />
                <h2 :class="inCartPanel ? 'text-lg font-medium' : 'text-[27px] font-semibold' "
                >Cart Items</h2>
                <ShoppingCartIcon class="w-6 h-6"/>
            </div>

            <CartItem 
                v-for="cartItem of cartItems.data" 
                :key="cartItem.product_id" 
                :cartItem="cartItem" :imgW="imgW" :imgH="imgH"
                :imgClass="imgClass"
            />

        </div> <!-- end of cart items container -->

        <!-- Total Price -->
        <h3 v-if="cartItems.data.length" class="text-lg text-right px-6 mt-5">
            Total: <span class="font-medium">{{ formatUSD(cartTotalPrice) }}</span>
        </h3>

        <!-- checkout button -->
        <div v-if="cartItems.data.length" class="flex justify-end gap-x-4 bg-white py-7 px-6">          
            <Link v-if="inCartPanel" :href="route('cart.index')" class="button-gray rounded-sm max-w-52 w-full font-medium">View Cart</Link>
        <button @click="checkout" class="button-dark rounded-sm max-w-52 w-full">Checkout</button>
        </div>

        <!-- show no items -->
        <p v-if="!cartItems.data.length" class="mt-5 px-6">No items in the cart</p>
</template>