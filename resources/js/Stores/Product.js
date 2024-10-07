import {ref} from 'vue';
import { httpGet } from '@/Helpers/http-helper';
import { router, useForm, usePage } from '@inertiajs/vue3';


export const productLoading = ref(false);
export const productForm = useForm({
    id:'',
    title:'',
    description:'',
    price:'',
    image:null,
    published: false,
});

export const getProduct = async (productId)=>{
    productLoading.value = true;
    const response = await httpGet( route('products.edit', productId) ); 
    productForm.id = response.data.id;
    productForm.title = response.data.title;
    productForm.description = response.data.description;
    productForm.price = response.data.price;
    productForm.published = response.data.published;
    productLoading.value = false;
};
  
export const updateProduct = (callback)=>{
    productLoading.value = true;
    productForm._method = 'put';
    router.post( route( 'products.update', productForm.id ), productForm,{
        onSuccess:()=>{ callback() },
        onFinish: ()=>{ 
            productForm.clearErrors(),
            productLoading.value = false;
        }
    });
    
};

export const addProduct = (callback)=> {
    productForm.post( route( 'products.store'),{
        onSuccess:()=>{ callback() },
        onFinish: ()=>{ productForm.clearErrors() }
     });
}

export const deleteProduct = (productId)=>{
    const confirm = window.confirm('Are you sure to delete the product');
    if(!confirm) return;
    router.delete(route('products.destroy', productId));
}