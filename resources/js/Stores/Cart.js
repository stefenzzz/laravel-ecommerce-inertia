import { ref, reactive, computed } from 'vue';
import { httpPost } from '@/Helpers/http-helper.js';
import { showSuccessNotification } from '@/event-bus.js';
import { pulseLoading } from '@/Stores/Extra.js';

export const cartItems = reactive({ data:[] });
export const cartCount = ref();
export const showCartPanel = ref(false);

export const addToCart = async(productId, quantity = '')=>{
    try{
        const response = await httpPost(route('cart.add',productId),{quantity:quantity});
        cartCount.value = response.count;
        const index = getIndex(response.cartItem.product_id);
        if(index >=0){
            // update the quantity if cart item exists in cartItems
            cartItems.data[index].quantity = response.cartItem.quantity; 
        }else {
            // add the cartItem if it doesnt exists in cartItems
            cartItems.data.unshift( response.cartItem);
        }
        showSuccessNotification(`Item added to the cart total: ${cartCount.value}`);
    }catch(e){
        console.log(e);
    }

}

export const updateCart = async(productId, quantity)=>{
    try{      
        const response = await httpPost(route('cart.update',productId),
         {
             _method:'put',
            quantity:quantity 

         });
        const index = getIndex(productId);
        cartItems.data[index].quantity = quantity; // update quantity
        cartCount.value = response.count;   
        showSuccessNotification(`Updated item total count: ${quantity}`);
    }catch(e){
        console.log(e);
    }
}

export const removeFromCart = async(productId, element)=>{
    try{      
        const response = await httpPost(route('cart.remove',productId), {_method:'delete'});
        element.style.opacity = 0; // hide the cart item element
        setTimeout( ()=> { 
            const index = getIndex(productId);
            cartItems.data.splice(index,1); // remove it            
        } , 300);
        cartCount.value = response.count;    
        showSuccessNotification(`Item remove from the cart total: ${cartCount.value}`);
    }catch(e){
        console.log(e);
    }
}

const getIndex = (productId)=>{

   return  cartItems.data.findIndex(cartItem => cartItem.product_id == productId);
}