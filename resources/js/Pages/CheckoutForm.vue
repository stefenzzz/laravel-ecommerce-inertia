<script setup>
import CustomInput from '@/Components/CustomInput.vue'; 
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { pulseLoading } from '@/Stores/Extra.js';
import CheckoutItems from '@/Components/app/CheckoutItems.vue';
import PasswordInputToggle from '@/Components/PasswordInputToggle.vue';

const props = defineProps({
    errors:[String, Boolean, Object],
    countries: Array,
});

const countryOptions = props.countries.map( country=>({
    key: country.code,
    text: country.name
}));


const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    shippingAddress1: '',
    shippingAddress2: '',
    shippingCity: '',
    shippingZipcode: '',
    shippingCountry: 'default',
    shippingState: '',
    billingAddress1: '',
    billingAddress2: '',
    billingCity: '',
    billingZipcode: '',
    billingCountry: 'default',
    billingState: '',
});


const copy = ref(false);

const back = ()=>{
    window.history.back()
}

const submitForm = ()=>{
    pulseLoading.value = true;
    form.post(route('checkout.register'),{
        onFinish: ()=> pulseLoading.value = false,
    });
}

const copyShippingToBilling = ()=>{
    if(copy.value){
        form.billingAddress1 = form.shippingAddress1;
        form.billingAddress2 = form.shippingAddress2;
        form.billingCity = form.shippingCity;
        form.billingZipcode = form.shippingZipcode;
        form.billingCountry = form.shippingCountry;
        form.billingState = form.shippingState;
    }else{
        form.billingAddress1 = '';
        form.billingAddress2 = '';
        form.billingCity = '';
        form.billingZipcode = '';
        form.billingCountry = '';
        form.billingState = '';
    }
}


</script>


<template>

<section class="grid md:grid-cols-2 max-w-6xl mx-auto gap-x-5 px-4">

    <form class="space-y-2 bg-slate-50 px-6 py-4 rounded-md shadow-md">
        <h1 class="text-lg font-medium">Checkout Form</h1>
        <div class="grid md:grid-cols-2 gap-3">         
            <CustomInput v-model="form.first_name" name="first_name" placeholder="First Name" :message="errors.first_name"/>
            <CustomInput v-model="form.last_name" name="last_name" placeholder="Last Name" :message="errors.last_name"/>
            <CustomInput v-model="form.email" name="email" placeholder="Email Address" type="email" :message="errors.email"/>
            <CustomInput v-model="form.phone" name="phone" placeholder="Phone number" type="number" :message="errors.phone"/>
            <PasswordInputToggle v-model="form.password" name="password" placeholder="Password" :message="errors.password"/>   
            <PasswordInputToggle v-model="form.password_confirmation" placeholder="Retype Password" :message="errors.password_confirmation"/>   
        </div>

        <h3 class="text-lg font-medium">Shipping Address</h3>
        <div class="grid md:grid-cols-2 gap-3">
            <CustomInput v-model="form.shippingAddress1" name="shipping_address_1" placeholder="Address 1" :message="errors.shippingAddress1"/>
            <CustomInput v-model="form.shippingAddress2" name="shipping_address_2" placeholder="Address 2" :message="errors.shippingAddress2"/>
            <CustomInput v-model="form.shippingCity" name="shipping_city" placeholder="City" :message="errors.shippingCity"/>
            <CustomInput v-model="form.shippingZipcode" name="shipping_zipcode" placeholder="Zip Code" type="number" :message="errors.shippingZipcode"/>
            <CustomInput v-model="form.shippingCountry" name="shipping_coutnry" 
                type="select" placeholder="Select Country"
                :selectOptions="countryOptions" 
                :message="errors.shippingCountry"
            />
            <CustomInput v-model="form.shippingState" name="shipping_state" placeholder="State" :message="errors.shippingState"/>
        </div>

        <div>
            <div class="my-5 flex gap-x-2 items-center">
            <Checkbox id="shippingBilling" @change="copyShippingToBilling" v-model:checked="copy"/>
            <label class="text-sm pt-[1px]" for="shippingBilling">Is your shipping address the same as your billing address?</label>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-medium mt-5">Billing Address</h3>
        </div>

        <div class="grid md:grid-cols-2 gap-3">
            <CustomInput v-model="form.billingAddress1" name="billing_address_1" placeholder="Address 1" :message="errors.billingAddress1"/>
            <CustomInput v-model="form.billingAddress2" name="billing_address_2" placeholder="Address 2" :message="errors.billingAddress2"/>
            <CustomInput v-model="form.billingCity" name="billing_city" placeholder="City" :message="errors.billingCity"/>
            <CustomInput v-model="form.billingZipcode" name="billing_zipcode" placeholder="Zip Code" type="number" :message="errors.billingZipcode"/>
            <CustomInput v-model="form.billingCountry" name="billing_country"
                placeholder="Select Country" 
                type="select" :selectOptions="countryOptions"
                :message="errors.billingCountry"
             />
            <CustomInput v-model="form.billingState" name="billing_state" placeholder="State" :message="errors.billingState"/>
        </div>

        <div>
            <div class="mt-5 flex justify-between">
            <SecondaryButton @click="back">Back</SecondaryButton>
            <SecondaryButton @click="submitForm">Proceed</SecondaryButton>
            </div>
        </div>
    </form>
    <CheckoutItems class="hidden md:block"/>
</section>

</template>