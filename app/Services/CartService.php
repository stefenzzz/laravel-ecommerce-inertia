<?php

namespace App\Services;
use App\Helpers\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Contracts\CartServiceInterface;
use App\Models\User;



/**
 * Class CartService
 *
 * Manages cart operations for guest and authenticated users.
 * Handles adding, updating, and removing products in the cart.
 * Guest cart data is stored in cookies, while authenticated users 
 * have their cart data saved in the database.
 *
 * @package App\Services
 */

class CartService implements CartServiceInterface
{
    // we need to get the cart items from cookie in each method to track the updates in cookie
    
    // Guest Methods

    /**
     * Add a product to the cart for a guest user.
     *
     * @param Product $product  The product to add to the cart.
     * @param int $quantity      The quantity of the product to add.
     * @return array             Returns the updated cart count and the cart item details.
     */
    public function add(Product $product, int $quantity)
    {
        // get cart items from cookie
        $cartItems = Cart::getCartItemsFromCookie(); 

        // if item already saved in cookie then update quantity
        if( isset($cartItems[$product->id]) ){    
            $cartItems[$product->id]['quantity'] += $quantity; 
        }else{
            $cartItems[$product->id] = [
                'product_id' => $product->id,
                'quantity' => $quantity
            ];
        }

        Cart::addToCookie($cartItems);            
        return [
                'count' => Cart::getCount($cartItems), 
                'cartItem' => [
                    'product' => $product,
                    'product_id' => $product->id,
                    'quantity' =>$cartItems[$product->id]['quantity']
                ] 
            ];
    }

     /**
     * Update the quantity of a product in the cart for a guest user.
     *
     * @param Product $product  The product to update in the cart.
     * @param int $quantity      The new quantity of the product.
     * @return array             Returns the updated cart count.
     */
    public function update(Product $product, int $quantity)
    {
        // get cart items from cookie
        $cartItems = Cart::getCartItemsFromCookie();

        //replace the quantity if exists
        if(isset($cartItems[$product->id])){
            $cartItems[$product->id]['quantity'] = $quantity;
        }

        Cart::addToCookie($cartItems);
        return [ 'count' => Cart::getCount($cartItems)];
    }

    /**
     * Remove a product from the cart for a guest user.
     *
     * @param Product $product  The product to remove from the cart.
     * @return array             Returns the updated cart count.
     */
    public function delete(Product $product)
    {
        // get cart_items from cookie
        $cartItems = Cart::getCartItemsFromCookie();

        // if id exist in cart_items as an index key, unset/remove it
        if(isset($cartItems[$product->id])) unset($cartItems[$product->id]);
        
        Cart::addToCookie($cartItems);
        return [ 'count' => Cart::getCount($cartItems) ]; 
    }



    // Authenticated user-specific methods

     /**
     * Add a product to the cart for an authenticated user.
     *
     * @param Product $product  The product to add to the cart.
     * @param int $quantity      The quantity of the product to add.
     * @param User $user         The authenticated user.
     * @return array             Returns the updated cart count and cart item details.
     */
    public function addForUser(Product $product, int $quantity, User $user)
    {
        $cartItems = $user->cartItems; 
        $existingCartItem = $cartItems->firstWhere('product_id', $product->id);

        if($existingCartItem){
             $existingCartItem->quantity += $quantity; // udpate if quantity is already exists
             $existingCartItem->save();
        }else{
           $cartItem =  $user->cartItems()->create([
                 'product_id' => $product->id,
                 'quantity' => $quantity,
             ]);
        }
         $user->load('cartItems'); // eager load to update cartItems

         return [ 
             'count' => $user->cartItems->sum('quantity'),
             'cartItem' => [
                 'product' => $product,
                 'product_id' => $product->id,
                 'quantity' => $existingCartItem->quantity ?? $cartItem->quantity,
             ]
         ];
    }

     /**
     * Update the quantity of a product in the cart for an authenticated user.
     *
     * @param Product $product  The product to update in the cart.
     * @param int $quantity      The new quantity of the product.
     * @param User $user         The authenticated user.
     * @return array             Returns the updated cart count.
     */
    public function updateForUser(Product $product, int $quantity, User $user)
    {
        
        $cartItems = $user->cartItems;

        $existingCartItem = $cartItems->where('product_id', $product->id)->first();

        if($existingCartItem){

            $existingCartItem->quantity = $quantity;
            $existingCartItem->save();

        }else{
            // output no data to update
        }
        
        $user->load('cartItems');
        return [ 'count' => $user->cartItems->sum('quantity') ];
    }

     /**
     * Remove a product from the cart for an authenticated user.
     *
     * @param Product $product  The product to remove from the cart.
     * @param User $user         The authenticated user.
     * @return array             Returns the updated cart count.
     */
    public function deleteForUser(Product $product, User $user)
    {
        $cartItems = $user->cartItems;
        $existingCartItem = $cartItems->where('product_id', $product->id)->first();

        if($existingCartItem){
            $existingCartItem->delete();
        }else{
            // output no data to delete
        }            
        $user->load('cartItems');
        return [ 'count' => $user->cartItems->sum('quantity') ];
    }
}
