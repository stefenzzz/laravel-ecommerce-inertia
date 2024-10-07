<script setup>
import ProductModal from '@/Components/app/ProductModal.vue';
import { ref, reactive, computed } from 'vue';
import { formatDate, formatUSD } from '@/Helpers/format.js';
import { PencilSquareIcon } from '@heroicons/vue/24/solid';
import { TrashIcon } from '@heroicons/vue/24/outline';
import { deleteProduct } from '@/Stores/Product.js';

const showModal = ref(false);
const productId = ref('');

defineProps({
    products: [Object, Array],
});

const closeModal = ()=>{
  showModal.value = false;
  productId.value = '';
}

const showEditProduct = (id)=>{
  productId.value = id;
  showModal.value = true;
}


</script>

<template>
<section class="max-w-[1278px] mx-auto">
    
    <div class="flex items-center justify-between mb-3">
      <h1 class="ml-3 text-2xl">Products</h1>
      <button type="button" @click="showModal = true" class="addButton">Add new Product</button>
    </div>
    
    <table class="shadow-around rounded-md" width="100%">
        <thead>
            <tr>
                <td class="border-b py-2 text-left cursor-pointer px-6 w-[5%]">ID</td>
                <td class="border-b py-2 text-left cursor-pointer px-6 w-[10%]">Image</td>
                <td class="border-b py-2 text-left cursor-pointer px-6 w-[30%]">Title</td>
                <td class="border-b py-2 text-left cursor-pointer px-6 w-[10%]">Price</td>
                <td class="border-b py-2 text-left cursor-pointer px-6 w-[15%]">Last Updated At</td>
                <td class="border-b py-2 text-left cursor-pointer px-6 w-[5%]">Actions</td>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(product, index) of products.data">
                <td class="border-b py-2 px-6" >{{ product.id }}</td>
                <td class="border-b py-2 px-6" ><img class="w-16 h-16" :src="product.image_url" :alt="product.title"></td>
                <td class="border-b py-2 px-6" >{{ product.title }}</td>
                <td class="border-b py-2 px-6" >{{ formatUSD(product.price) }}</td>
                <td class="border-b py-2 px-6" >{{ formatDate(product.updated_at) }}</td>
                <td class="border-b py-2 px-6" >
                  <div class="flex gap-x-2">            
                    <PencilSquareIcon class="h-6 w-6 text-blue-400 cursor-pointer" @click="showEditProduct(product.id)"/>
                    <TrashIcon class="h-6 w-6 text-blue-400 cursor-pointer" @click="deleteProduct(product.id)"/>
                  </div>
                </td>
            </tr>
        </tbody>
    </table>
    
    <!-- pagination -->
    <div class="flex flex-col md:flex-row items-center justify-between mt-5">

        <div class="flex gap-x-1">
            <span>Showing {{ products.meta.from }}</span> 
            <span>to {{ products.meta.to }}</span>
            <span>of {{ products.meta.total }} results</span>
         </div>

        <div>
          <Link
            v-for="(link, i) of products.meta.links"
            :key="i"
            v-html="link.label"          
            :class="`cursor-pointer inline-flex items-center px-3 md:px-4 py-2 border text-sm font-medium whitespace-nowrap
                ${ link.active  ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'}
                ${ i === 0 && 'rounded-l-md' }
                ${ i === products.links.length - 1 && 'rounded-r-md' }
                ${ !link.url ? ' bg-gray-100 text-gray-400': 'text-gray-600' } `" 
            :href="link.url || '#' "
            :disabled="!link.url"
          ></Link>
        </div>
    </div>
    <!-- pop up modal -->
    <ProductModal v-model="showModal" @close="closeModal()" :productId="productId"/>

</section>
</template>