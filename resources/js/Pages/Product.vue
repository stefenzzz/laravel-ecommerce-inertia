<script setup>
import Reviews from '@/Components/app/Reviews.vue';
import ProductDetails from '@/Components/app/ProductDetails.vue';
defineProps({
    product: Object,
});
</script>

<template>
    <section class="max-w-[1100px] mx-auto px-3">
        <ProductDetails :product="product"/>
        <Reviews />
    </section>
</template>