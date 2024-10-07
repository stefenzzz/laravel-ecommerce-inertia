<script setup>
import ProductLists from '@/Components/app/ProductLists.vue';
import SearchProduct from '@/Components/app/SearchProduct.vue';
defineProps({
    products:[Array, Object],
});
</script>

<template>
        <SearchProduct/>
        <ProductLists :products="products"/>
</template>