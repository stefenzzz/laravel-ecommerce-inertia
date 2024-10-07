<script setup>
import UserSettingsDropdown from '@/Components/app/UserSettingsDropdown.vue';
import { ShoppingCartIcon } from '@heroicons/vue/24/outline';
import CartCount from '@/Components/app/CartCount.vue';
import { cartCount, showCartPanel } from '@/Stores/Cart.js';
</script>

<template>
    <header class="bg-white border-b fixed z-20 w-full">
        <nav class="flex justify-between items-center max-w-[1175px] mx-auto py-5">
            <Link :href="route('home')" class="px-3 py-1 uppercase">Store</Link>
            

            <div class="flex items-center gap-x-5">
                <button @click="showCartPanel = !showCartPanel" class="relative flex items-center gap-x-1">
                     <ShoppingCartIcon class="w-5 h-5"/> Cart
                     <CartCount v-model="cartCount" />
                </button>
                <UserSettingsDropdown v-if="$page.props.auth.user"/>           
                <div v-else class="flex items-center gap-x-5">
                    <Link class="px-3 py-1" :class="{ 'active': $page.props.route.name === 'login' }" :href="route('login')" >Login</Link>            
                    <Link class="px-3 py-1" :class="{ 'active': $page.props.route.name === 'register' }" :href="route('register')" >Register</Link>
                </div>
            </div>
        </nav>
    </header>
</template>