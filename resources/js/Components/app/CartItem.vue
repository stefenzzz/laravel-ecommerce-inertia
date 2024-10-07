<script setup>
import { removeFromCart, updateCart} from '@/Stores/Cart.js';
import ItemCounter from '@/Components/ItemCounter.vue';
import { formatUSD } from '@/Helpers/format.js';
import { computed, ref, onMounted, watch} from 'vue';
import { TrashIcon } from '@heroicons/vue/24/outline';
import ProductRemoved from '@/Components/app/ProductRemoved.vue';

const props = defineProps({
    cartItem: Object,
    removeIcon: {
        type: Boolean,
        default: false
    },
    imgW: String,
    imgH: String,
    imgClass: String,
})
const itemRef = ref(null); // ref the article element for deleiting will add transition opacity 0
const quantity = computed({
    get(){
        return props.cartItem.quantity
    },
    set(newQuantity){
        updateCart(props.cartItem.product_id, newQuantity ); // update cart item quantity
        
    }
});

const smallDevice = ref(window.innerWidth < 450);

</script>

<template>
        <!-- check if product exists -->
        <article v-if="cartItem.product.status !== 'removed' " class="flex gap-x-4 border-b pb-5 transition-[ease_300ms]" :ref="el=> itemRef = el" > 
            <Link :href="route('product.view', cartItem.product.slug)">
                <img 
                :width="imgW" 
                :height="imgH" 
                :class="[ 'mx-auto', imgClass ]"
                :src="cartItem.product.image" :alt="cartItem.product.title">
            </Link>

            <div class="flex-1 space-y-5">
                <div class="flex items-start justify-between gap-x-3">
                    <h3 class="font-medium">{{ cartItem.product.title }}</h3>                      
                    <span>{{ formatUSD( cartItem.product.price * cartItem.quantity ) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-x-3">
                        Quantity: 
                        <span>{{ quantity }}</span>
                        <ItemCounter v-model="quantity" max="50"/>
                    </span>                  
                    <TrashIcon v-if="removeIcon || smallDevice" class="w-6 h-6 text-gray-500 cursor-pointer" @click="removeFromCart(cartItem.product_id, itemRef)"/>
                    <button v-else @click="removeFromCart(cartItem.product_id, itemRef)" class="button-gray rounded-sm" >Remove</button>
                </div>
            </div>
        </article>
        <!-- else product not exists -->
        <ProductRemoved v-else :imgH="imgH" :imgW="imgW" :imgClass="imgClass" :cartItem="cartItem"/>
</template>