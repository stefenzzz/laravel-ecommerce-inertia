<script setup>
import CheckoutItem from '@/Components/app/CheckoutItem.vue';
import { ShoppingCartIcon, ChevronDoubleLeftIcon } from '@heroicons/vue/24/solid';
import {computed, ref} from 'vue';
import { formatUSD, formatLongDate } from '@/Helpers/format.js';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    order: Object,
    session: Object,
});

const orderTotalPrice = computed(()=>{
    return props.order.items.reduce((acc, e)=> acc += Number(e.quantity) * parseFloat(e.product.price), 0);
 });

const removedItem = computed(()=>{
    return props.order.items.filter((e)=> e.product.status === 'removed');
});

</script>

<template>

    <section class="max-w-5xl mx-auto grid md:grid-cols-2 px-4">
        
        <!-- first column -->
        <div class="space-y-6 px-6 md:px-8 py-6 bg-slate-50 shadow-md">
            <h1 class="text-lg font-medium flex gap-x-2 items-center">              
                <ChevronDoubleLeftIcon class="w-5 h-5 cursor-pointer hover:text-gray-500" @click="router.get(route('orders'))"/>  
                Order # {{ order.id }}
                <ShoppingCartIcon class="w-5 h-5"/>
            </h1>  
            <div class="space-y-6 max-h-[530px] w-full overflow-auto cart-container pr-4">
                 <CheckoutItem class="max-w-md border-b pb-3" v-for="item of order.items" :key="order.items.id" :item="item" />   
            </div>    
            <h3 class="max-w-md text-lg text-right">Total: {{ formatUSD(orderTotalPrice) }}</h3>
        </div>

        <!-- second column -->
        <div v-if="session" class="mt-5 max-w-6xl mx-auto rounded-md px-4 py-2 leading-6 font-mono">
            <p>Stripe Payment</p>
            <p class="capitalize">Payment Status: {{ session.payment_status }}</p>
            <p>Total Amount: {{ formatUSD(Number(session.amount_total) / 100)  }}</p>
            <p>Payment Expiration: {{ formatLongDate( session.expires_at * 1000 ) }}</p>
            <p>Payment Result: 
                <span v-if="session.payment_status === 'paid'" class="text-green-600">Success</span>
                <span v-else class="text-red-500">Pending</span>
            </p>
            <p v-if="session.url && removedItem.length <= 0">Payment Link: 
                <a :href="session.url"
                rel="nofollow noopener noreferrer" 
                class="text-blue-500 underline"
                >
                Stripe
                </a>
            </p>
        </div>
        
    </section>
</template> 