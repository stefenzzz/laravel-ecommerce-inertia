<script setup>
import {formatDate, formatUSD} from '@/Helpers/format.js';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Array
});

</script>

<template>
    
    <section class=" max-w-6xl px-2 mx-auto bg-slate-50">
        <div class="grid grid-cols-6 md:grid-cols-[10%_20%_20%_20%_15%_15%] border-b font-semibold">
            <span class="py-2 px-2 md:px-6">Order</span>
            <span class="py-2 px-2 md:px-6">Date</span>
            <span class="py-2 px-2 md:px-6">Status</span>
            <span class="py-2 px-2 md:px-6">Subtotal</span>
            <span class="py-2 px-2 md:px-6 text-center md:text-left">Items</span>
            <span class="py-2 px-2 md:px-6">Actions</span>
        </div>
        <article v-for="order of orders.data" :key="order.id">
            <ul @click="router.get(route('orders.view', order.id))"
             class="grid grid-cols-6 md:grid-cols-[10%_20%_20%_20%_15%_15%]
              items-center border-b text-sm hover:bg-blue-50 cursor-pointer"
             >
                <li class="py-3 px-2 md:px-6 text-blue-600"># {{ order.id }}</li>
                <li class="py-3 px-2 md:px-6">{{ formatDate(order.created_at) }}</li>
                <li class="py-3 px-2 md:px-6">
                    <span
                     :class="[ 
                        `inline-block py-2 px-1 md:px-2 text-xs font-medium tracking-wide
                         rounded-md uppercase max-w-[70px] w-full text-center text-white`,
                        order.is_paid ? 'bg-emerald-500 ' : 'bg-gray-400',
                     ]"
                     >{{ order.status }}</span>
                </li>
                <li class="py-3 px-2 md:px-6">{{ formatUSD(order.total_price) }}</li>
                <li class="py-3 px-2 md:px-6 text-center md:text-left">{{ order.items_count }}</li>
                <li class="py-3 px-2 md:px-6">
                     <a v-if="!order.is_paid" 
                     class="
                     inline-block bg-purple-500 hover:bg-purple-600 max-w-14
                     w-full py-1 px-1 md:px-2 text-white text-center
                     font-medium uppercase rounded-md transition-[300ms_ease] cursor-pointer" 
                     @click.stop=" router.post(route('checkout.order', order.id)) "
                     >
                     Pay
                    </a> 
                </li>
            </ul>
        </article>
        <p v-if="!orders.data.length" class="py-5 px-2"># No Orders</p>
    </section>
    
</template>