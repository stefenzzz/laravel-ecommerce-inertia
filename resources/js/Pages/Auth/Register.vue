<script setup>
import { useForm } from '@inertiajs/vue3';
import CustomInput from '@/Components/CustomInput.vue';
import PasswordInputToggle from '@/Components/PasswordInputToggle.vue';
import { pulseLoading } from '@/Stores/Extra.js'

defineProps({
    errors:Object
});


const form = useForm({
    first_name:'',
    last_name:'',
    email:'',
    password:'',
    password_confirmation:'',
});

const submit = ()=>{
    pulseLoading.value = true;
    form.post(route('register'),{
        onError: ()=> form.reset('password','password_confirmation'),
        onFinish:()=> pulseLoading.value = false,
    });
}

</script>

<template>

    <section>
        <form @submit.prevent="submit" 
        :class="['space-y-6 bg-white mx-white max-w-96 mx-auto p-6 rounded-md shadow-around', {'opacity-50':form.processing}]">

            <h1 class="text-center text-2xl font-semibold">Register</h1>            
            <CustomInput type="text" v-model="form.first_name"  name="first_name" placeholder="First Name" :message="errors.name"/>
            <CustomInput type="text" v-model="form.last_name" name="last_name" placeholder="Last Name" :message="errors.name"/>
            <CustomInput type="text" v-model="form.email" name="email" placeholder="Email" :message="errors.email"/>
            <PasswordInputToggle v-model="form.password" name="password" placeholder="Password" :message="errors.password"/>   
            <PasswordInputToggle v-model="form.password_confirmation" placeholder="Retype Password" :message="errors.password_confirmation"/>   
            <div>
                <p class="text-sm">Already have an account? <Link :href="route('login')" class="text-blue-500 font-semibold">Login</Link></p>
            </div>
            <button class="button-gray" :disable="form.processing">Submit</button>
        </form>
    </section>
    
</template>