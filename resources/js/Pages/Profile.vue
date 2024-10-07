<script setup>
import CustomInput from '@/Components/CustomInput.vue'; 
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { pulseLoading } from '@/Stores/Extra.js';
import PasswordInputToggle from '@/Components/PasswordInputToggle.vue';

const props = defineProps({
    errors:[ String, Boolean, Object ],
    countries: Array,
    user: Object,
    shipping: Object,
    billing: Object,
});

const countryOptions = props.countries.map( country=>({
    key: country.code,
    text: country.name
}));

const form = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    phone: props.user.phone,
    password: '',
    password_confirmation: '',
    shipping:{
        address1: props.shipping.address1,
        address2: props.shipping.address2,
        city: props.shipping.city,
        zipcode: props.shipping.zipcode,
        country_code: props.shipping.country_code ?? 'default',
        state: props.shipping.state,
    },
    billing:{
        address1: props.billing.address1,
        address2: props.billing.address2,
        city: props.billing.city,
        zipcode: props.billing.zipcode,
        country_code: props.billing.country_code ?? 'default',
        state: props.billing.state,
    }

});

const copy = ref(false);

const submitForm = ()=>{
    pulseLoading.value = true;
    form.put(route('profile.update', props.user.id ),{
        onFinish: ()=> pulseLoading.value = false,
    });
}

const copyShippingToBilling = ()=>{

    if(copy.value){
        form.billing.address1 = form.shipping.address1;
        form.billing.address2 = form.shipping.address2;
        form.billing.city = form.shipping.city;
        form.billing.zipcode = form.shipping.zipcode;
        form.billing.country_code = form.shipping.country_code;
        form.billing.state = form.shipping.state;
    }else{
        form.billing.address1 = '';
        form.billing.address2 = '';
        form.billing.city = '';
        form.billing.zipcode = '';
        form.billing.country = '';
        form.billing.state = '';
    }
}
</script>


<template>

    <section class="max-w-2xl mx-auto gap-x-5 px-4">
    
        <form class="space-y-3 bg-slate-50 px-6 py-4 rounded-md shadow-md">
            <h1 class="text-lg font-medium">Personal Info</h1>
            <div class="grid md:grid-cols-2 gap-3">         
                <CustomInput v-model="form.first_name" name="first_name" placeholder="First Name" :message="errors.first_name"/>
                <CustomInput v-model="form.last_name" name="last_name" placeholder="Last Name" :message="errors.last_name"/>
                <CustomInput v-model="form.phone" name="phone" placeholder="Phone number" type="number" :message="errors.phone"/>
            </div>
            
            <!-- <div class="grid md:grid-cols-2 gap-3 hidden">
                <PasswordInputToggle v-model="form.password" name="password" placeholder="Password" :message="errors.password"/>   
                <PasswordInputToggle v-model="form.password_confirmation" placeholder="Retype Password" :message="errors.password_confirmation"/>   
            </div> -->
    
            <h3 class="text-lg font-medium">Shipping Address</h3>
            <div class="grid md:grid-cols-2 gap-3">
                <CustomInput v-model="form.shipping.address1" name="shipping_address_1" placeholder="Address 1" :message="errors['shipping.address1']"/>
                <CustomInput v-model="form.shipping.address2" name="shipping_address_2" placeholder="Address 2" :message="errors['shipping.address2']"/>
                <CustomInput v-model="form.shipping.city" name="shipping_city" placeholder="City" :message="errors['shipping.city']"/>
                <CustomInput v-model="form.shipping.zipcode" name="shipping_zipcode" placeholder="Zip Code" type="number" :message="errors['shipping.zipcode']"/>
                <CustomInput v-model="form.shipping.country_code" name="shipping_coutnry" 
                    type="select" placeholder="Select Country"
                    :selectOptions="countryOptions" 
                    :message="errors['shipping.country_code']"
                />
                <CustomInput v-model="form.shipping.state" name="shipping_state" placeholder="State" :message="errors['shipping.state']"/>
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
                <CustomInput v-model="form.billing.address1" name="billing_address_1" placeholder="Address 1" :message="errors['billing.address1']"/>
                <CustomInput v-model="form.billing.address2" name="billing_address_2" placeholder="Address 2" :message="errors['billing.address2']"/>
                <CustomInput v-model="form.billing.city" name="billing_city" placeholder="City" :message="errors['billing.city']"/>
                <CustomInput v-model="form.billing.zipcode" name="billing_zipcode" placeholder="Zip Code" type="number" :message="errors['billing.zipcode']"/>
                <CustomInput v-model="form.billing.country_code" name="billing_country"
                    placeholder="Select Country" 
                    type="select" :selectOptions="countryOptions"
                    :message="errors['billing.country_code']"
                 />
                <CustomInput v-model="form.billing.state" name="billing_state" placeholder="State" :message="errors['billing.state']"/>
            </div>
    
            <div>
                <div class="mt-5">
                <SecondaryButton @click="submitForm">Update</SecondaryButton>
                </div>
            </div>
        </form>
    </section>
    
</template>