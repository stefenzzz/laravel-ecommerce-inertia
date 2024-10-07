<script setup>
// Imports
import {Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue'
import {ChevronDownIcon} from '@heroicons/vue/20/solid'
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';  

// use
const form = useForm({});

// methods
const logout = ()=>{
    const confirm = window.confirm('Are you sure to logout');
    if(!confirm) return;
    form.post(route('logout'))
    
}

</script>


<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                class="inline-flex w-full justify-center rounded-md px-4 py-2 font-medium text-gray-800
                hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white
                focus-visible:ring-opacity-75 capitalize"
            >
                {{ $page.props.auth.user.name }}

                <ChevronDownIcon
                    class="ml-2 -mr-1 h-5 w-5 text-gray-800"
                    aria-hidden="true"
                />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            >
                 <div class="px-1 py-1">
                    <MenuItem v-if="$page.props.auth.user.role === 'admin'" v-slot="{ active }">
                        <a @click="router.visit(route('products.index'))" :class="['navlink', {'text-gray-700 bg-gray-100': $page.props.route.name === 'products.index' }]">Products</a>
                    </MenuItem>

                    <MenuItem v-slot="{ active }">                   
                        <a @click="router.visit(route('orders'))" :class="['navlink', {'text-gray-700 bg-gray-100': $page.props.route.name === 'orders' }]">Orders</a>
                    </MenuItem>

                    <MenuItem v-slot="{ active }">                   
                        <a @click="router.visit(route('profile'))" :class="['navlink', {'text-gray-700 bg-gray-100': $page.props.route.name === 'profile' }]">Profile</a>
                    </MenuItem>

                    <MenuItem v-slot="{ active }">                    
                        <button @click="logout()" :class="'navlink'">Logout</button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>


