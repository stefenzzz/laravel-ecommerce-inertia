<script setup>
import ItemCounter from '@/Components/ItemCounter.vue';
import { addToCart } from '@/Stores/Cart.js';
import Ratings from '@/Components/Ratings.vue';
import ToggleDescription from '@/Components/ToggleDescription.vue';
import { formatUSD } from '@/Helpers/format.js';
import { computed, ref } from 'vue';
import PhotoModal from '@/Components/app/PhotoModal.vue';


const props = defineProps({
    product: Object,
});

const showPhotoModal = ref(false);
const showFullDescription = ref(false);
const initialDescriptionLength = 500;
const quantity = ref(1);
const productRate = ref(5.0); // used static rate for now

const toggleFullDescription = ()=>{
  showFullDescription.value = !showFullDescription.value;
}

const truncatedDescription = computed(()=>{
  let description = props.product.description;
  if(!showFullDescription.value){

     description = description.substring(0, initialDescriptionLength);
  }
  return description;
});

</script>

<template>
    <!-- show if product is not active -->
    <p v-if="product.status !== 'active' " class="text-lg text-red-500 mb-5">Product has been {{ product.status }}.</p>

    <!-- product container -->
    <article class="flex flex-col items-center md:items-start md:justify-start lg:flex-row gap-x-8">

    <!-- first column -->
    <div class="max-w-96 w-full">
        <figure class="w-full  cursor-pointer" @click="showPhotoModal = true">
            <img class="w-full":src="product.image" :alt="product.title">
        </figure>
        <div class="grid grid-cols-5 mt-4">
            <figure class=" cursor-pointer" @click="showPhotoModal = true">
                <img class="w-full":src="product.image" :alt="product.title">
            </figure>
        </div>
    </div>

    <!-- second column -->
    <div class="mt-3 max-w-full w-full">

        <!-- product title, price and rating -->
        <div class="grid items-start md:grid-cols-[auto_minmax(0,150px)] gap-x-4 gap-y-1 w-full ">
            
            <div class="flex flex-col gap-y-3 justify-start">
                <h1 class="text-2xl font-semibold leading-7">{{ product.title }}</h1> 
                <span class="block text-lg font-medium">{{ formatUSD(product.price * quantity) }}</span>
            </div>
            <div class="mt-3 md:mt-0 flex flex-row gap-x-3 justify-start md:flex-col md:items-end md:gap-y-2">
                <h3 class="text-xl md:text-right">Ratings({{ productRate.toFixed(1) }})</h3>
                <Ratings :rate="productRate" :starClass="'w-5 h-5 text-yellow-500'"/>
            </div>

        </div>

        <!-- adding quantity and adding to cart -->
        <div v-if="product.status === 'active' " class="flex items-center justify-between mt-10">
            <div class="flex items-center gap-x-4">
                <span>Quantity:</span>
                {{ quantity }}
                <ItemCounter v-model="quantity" max="20"/>
            </div>
            <button @click="addToCart(product.id, quantity)" class="button-gray rounded-sm">Add To Cart</button>
        </div>

        <!-- description -->
        <div class="mt-7">
            <h2 class="text-lg font-semibold">Description:</h2>
            <p class="leading-7 tracking-wide font-poppins font-light text-[15px]">{{ truncatedDescription }}
                <ToggleDescription 
                :length="truncatedDescription.length"
                :limit="initialDescriptionLength"
                @onToggle="toggleFullDescription"
                />
            </p>
        </div>

    </div><!-- end of second column -->
    
</article>

<PhotoModal v-model="showPhotoModal" :product="product"/>

</template>