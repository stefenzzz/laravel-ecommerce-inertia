<script setup>
import CustomInput from '@/Components/CustomInput.vue';
import { useForm } from '@inertiajs/vue3';
import PasswordInputToggle from '@/Components/PasswordInputToggle.vue';
import { pulseLoading } from '@/Stores/Extra.js'

defineProps({
    errors:Object
});

const form = useForm({
    email:'',
    password:'',
});

const submit = ()=>{
    pulseLoading.value = true;
    form.post(route('login'),{
        onError: ()=> form.reset('password'),
        onFinish:()=> pulseLoading.value = false,
    });
}

</script>

<template>  
    <section>
        <form @submit.prevent="submit"
         :class="['space-y-6 bg-white mx-white max-w-96 mx-auto p-6 rounded-md overflow-hidden shadow-around', {'opacity-50':form.processing} ]">

            <h1 class="text-center text-2xl font-semibold">Sign In</h1>
            <CustomInput type="text" v-model="form.email" name="email" placeholder="Email" :message="errors.email"/>
            <PasswordInputToggle v-model="form.password" name="password" placeholder="Password" :message="errors.password"/>
            
            <p class="text-sm">
                Don't have an account yet? 
                <Link :href="route('register')" class="text-blue-500 font-semibold">Register</Link>
            </p>
            <button class="button-gray" :disable="form.processing">Login</button>
        </form>
    </section>
</template>