<script setup>

import { TransitionChild, TransitionRoot} from '@headlessui/vue';
import { computed, onUpdated, reactive, ref, onMounted, watch } from 'vue';
import { XCircleIcon } from '@heroicons/vue/24/solid';
import { router, useForm, usePage } from '@inertiajs/vue3';
import CustomInput from '@/Components/CustomInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Spinner from '@/Components/Spinner.vue';
import { productForm, productLoading, updateProduct, addProduct, getProduct } from '@/Stores/Product';

const page = usePage();
const emits = defineEmits(['close']);
const model = defineModel();
const props = defineProps({
  productId: [String, Number],
})

const close = ()=>{
  productForm.reset();
  page.props.errors = {};
  emits('close');
}

const submitForm = ()=> {
  (productForm.id) ? updateProduct(close) : addProduct(close);


}


// check if props.productId has value when opening the modal
watch(
    ()=> props.productId, 
    (productId) => {
        if (productId) {
            getProduct(productId);
        }
    }
);


</script>
<!-- This example requires Tailwind CSS v2.0+ -->
<template>

  <TransitionRoot as="div" :show="model" class="relative z-50">   
    
    <!--transition black background -->
    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                      leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
      <div class="fixed inset-0 bg-black bg-opacity-20 transition-opacity" ></div> 
    </TransitionChild>

    <TransitionChild as="div" class="fixed z-10 inset-0"
                      enter="ease-out duration-300"
                      enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                      enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                      leave-from="opacity-100 translate-y-0 sm:scale-100"
                      leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

      <!--productForm container -->
      <div class="relative top-1/2 -translate-y-1/2 max-w-[600px] w-full mx-auto overflow-hidden bg-white rounded-lg shadow-xl">

        <header class="py-3 px-4 flex justify-between items-center">
          <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ productForm.id ? `Update product: "${ productForm.title}"` : 'Create new Product' }}
          </h3>

          <button @click="close"class="flex items-center justify-center rounded-full transition-colors cursor-pointer">
            <XCircleIcon class="text-gray-500 w-10 h-10"/>
          </button>                 
        </header>

        <form @submit.prevent="submitForm" class="bg-white px-6 pt-5 pb-4 space-y-6">
          
            <div>                      
              <CustomInput v-model="productForm.title" label="Product Title"/>
              <InputError :message="$page.props.errors.title"/>
            </div>

            <div>                     
              <CustomInput type="file" label="Product Image" @change="file => productForm.image = file"/>
                <InputError :message="$page.props.errors.image"/>
            </div>

            <div>                   
              <CustomInput type="textarea" v-model="productForm.description" label="Description" rows="10" customClass="leading-6 tracking-wide"/>
              <InputError :message="$page.props.errors.description"/>
            </div>

            <div>                     
              <CustomInput type="number" v-model="productForm.price" label="Price" customClass="input"/>
              <InputError :message="$page.props.errors.price"/>
            </div>

            <div>                     
              <CustomInput type="checkbox" v-model="productForm.published" label="Published"/>
              <InputError :message="$page.props.errors.published"/>
            </div>


          <footer class="bg-gray-50 flex justify-end gap-x-3 py-3"> 
            <DangerButton @click.prevent="close">Cancel</DangerButton>
            <PrimaryButton>{{ productForm.id ? 'Update': 'Create' }}</PrimaryButton>
          </footer>

          <Spinner v-if="productForm.processing || productLoading" class="absolute left-0 top-0 bg-[rgba(255,255,255,.5)] right-0 bottom-0 flex items-center justify-center"/>
        </form>
        
      </div> <!-- end of form container -->

    </TransitionChild>
  </TransitionRoot>
</template>
  