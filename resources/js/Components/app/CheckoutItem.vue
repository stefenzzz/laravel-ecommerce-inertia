<script setup>
import { formatUSD } from '@/Helpers/format.js';
defineProps({
    item:Object
});

</script>
<template>
    <article class="flex items-center gap-x-3">
        <Link :href="route('product.view', item.product.slug )">
            <img width="75" h="75" :src="item.product.image" :alt="item.product.title">
        </Link>

        <div class="flex-1 space-y-5">
                <div class="flex items-start justify-between gap-x-3">
                    <h3 class="font-medium">{{ item.product.title }}</h3>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-x-3">
                        Quantity: <span>{{ item.quantity }}</span>
                    </span>
                    <span>{{ formatUSD( item.product.price * item.quantity ) }}</span> 
                </div>
            </div>
    </article>
</template>