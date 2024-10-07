<script setup>
import Header from '@/Components/app/Header.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import { usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted, computed } from 'vue';
import { cartCount, cartItems, showCartPanel } from '@/Stores/Cart.js';
import CartPanel from '@/Components/app/CartPanel.vue';
import { pulseLoading } from '@/Stores/Extra.js';
import PulseLoader from '@/Components/PulseLoader.vue';
import { showNotifiation } from '@/event-bus';

const message = ref();
const page = usePage();

// set cart items and cart count from the server
onMounted(()=>{
    cartItems.data = page.props.cart.items;
    cartCount.value =  page.props.cart.count;
    if(page.props.flash.message) showNotifiation(page.props.flash.message, 2000);
});

// check props if there are changes from server
watch(()=> [page.props.cart.items, page.props.cart.count, page.props.flash.message],
    ([propsCartItems, propsCartCount, propsFlashMessage])=>{
    cartItems.data = propsCartItems;
    cartCount.value = propsCartCount;
    if(propsFlashMessage) showNotifiation(propsFlashMessage, 2000);
});
</script>

<template>
    <div>
        <Header/>
    </div>
    <main class="max-w-[1366px] mx-auto pt-36 pb-[230px]">
        <slot/>
    </main>
    <FlashMessage/>
    <CartPanel v-model="showCartPanel"/>
    <PulseLoader v-model="pulseLoading"/>
</template>