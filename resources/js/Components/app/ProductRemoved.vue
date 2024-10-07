<script setup>
import { TrashIcon } from '@heroicons/vue/24/outline';
import { removeFromCart, updateCart} from '@/Stores/Cart.js';
import { ref } from 'vue';

defineProps({
    imgW: String,
    imgH: String,
    imgClass: String,
    cartItem: Object,
    removeIcon: {
        type: Boolean,
        default: false
    },
});
const itemRef = ref(null); // ref the article element for deleiting will add transition opacity 0
const smallDevice = ref(window.innerWidth < 450);
</script>
<template>
    <article class="flex gap-x-4 border-b pb-5" ref="itemRef">
        <figure>
            <img 
            :width="imgW" 
            :height="imgH" 
            :class="[ 'mx-auto', imgClass ]"
            :src="cartItem.product.image" alt="No product">
        </figure>
        <div class="flex-1 space-y-3"> 

            <div>
                <h3 class="font-medium">{{cartItem.product.title}}</h3>
            </div>

            <div class="flex items-center gap-x-3 justify-between">
                <small>Product was removed</small>  
                <TrashIcon v-if="removeIcon || smallDevice" class="w-6 h-6 text-gray-500 cursor-pointer" @click="removeFromCart(cartItem.product_id, itemRef)"/>
                <button v-else @click="removeFromCart(cartItem.product_id, itemRef)" class="button-gray rounded-sm" >Remove</button>
            </div>
        </div>

    </article>
</template>