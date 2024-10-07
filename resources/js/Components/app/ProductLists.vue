<script setup> 
import ProductList from '@/Components/app/ProductList.vue';
import Spinner from '@/Components/Spinner.vue';
import { ref , onMounted } from 'vue';
import { httpGet } from '@/Helpers/http-helper.js';

// props
const props = defineProps({
    products:[Array, Object],
});
// refs
const loadMoreIntersect = ref(null);
const isLoading = ref(false);
const productLists = ref(props.products.data);
const nextPage = ref(props.products.next_page_url);

// methods
const loadMore = async()=>{
    if(nextPage.value === null) return;
    isLoading.value = true;
    const response = await httpGet(nextPage.value);
    productLists.value = [...productLists.value, ...response.data];
    nextPage.value = response.next_page_url;
    isLoading.value = false;
}

// hooks
onMounted(()=>{
    const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting && loadMore()), {
        rootMargin: '0px 0px -200px 0px'
    });
    observer.observe(loadMoreIntersect.value)
});

</script>

<template>
    
    <section class="grid grid-cols-2 gap-x-2 lg:grid-cols-[repeat(4,minmax(0,250px))] lg:gap-x-24 justify-center gap-y-16 mt-12 px-2">
        <!-- products iterate -->
        <ProductList v-for="product of productLists" :key="product.id" :product="product"/>  
    </section>

    <p v-if="!productLists.length" class="max-w-6xl mx-auto mt-5 px-4">No product found..</p>
    
    <!-- element when intersect load more products -->
    <div ref="loadMoreIntersect">
         <Spinner v-if="isLoading" class="pt-20"/>
    </div>
</template>