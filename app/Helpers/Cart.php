<?php

namespace App\Helpers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;


/**
 * Class Cart
 *
 * A helper class to manage cart operations for users, including retrieving, adding,
 * and moving cart items between cookies and the database.
 *
 * @package App\Helpers
 */
class Cart
{
    
    /**
     * Get the total count of cart items for the authenticated user
     * or from the cookie if the user is not logged in.
     *
     * @return int The total count of items in the cart.
     */
    public static function getCartItemsCount(?User $user = null): int
    {
        // return cart items count if user is logged in
        if($user){
            return $user->cartItems->sum('quantity');
        }
        
        // return cart items count from cookie if not logged in
        return self::getCount( self::getCartItemsFromCookie() );
        
    }

    /**
     * Retrieve cart items from the cookie.
     * If the cookie does not exist, an empty array is returned.
     *
     * @return array The array of cart items from the cookie.
     */
    public static function getCartItemsFromCookie()
    {
        // retrieve the cart items from cookie, if cookie didnt exists return empty array
        return json_decode(Cookie::get('cart_items', '[]'), true);
    }


    /**
     * Add cart items to the cookie, storing them as a JSON string.
     *
     * @param array $cartItems An associative array of cart items to be stored in the cookie.
     * @return void
     */
    public static function addToCookie($cartItems)
    {
        // add cartItems associative array to cart_items cookie
        Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30); // insert / update cookie
        
    }


    /**
     * Delete the specified cookie, defaulting to 'cart_items'.
     *
     * @param string $key The name of the cookie to delete. Defaults to 'cart_items'.
     * @return void
     */
    public static function deleteCookie(string $key = 'cart_items')
    {
        Cookie::queue(Cookie::forget($key));
    }


    /**
     * Get the total count of items from the given cart items array.
     *
     * @param array $cartItems An array of cart items.
     * @return int The total count of items.
     */
    public static function getCount($cartItems){

        return array_reduce( $cartItems,
         fn($carry, $item) => $carry + $item['quantity'],
            0
        );
    }

    /**
     * Get cart items along with their associated products for a given user.
     * If the user is not specified, cart items are retrieved from the cookie.
     * and products that are permanenlty removed in the system are exclude from the cart.
     * 
     * @param User|null $user The user whose cart items are to be retrieved, or null for cookie items.
     * @return array An array of cart items with product details.
     */
    public static function getCartItemsWithProducts(User|null $user)
    {   
        if($user) { // if user is logged in

            $cartItems = $user->cartItems()->with('product')->get()->toArray();
            return array_reverse($cartItems);

        }else { // if user is guest
            $cartItems = Cart::getCartItemsFromCookie(); 
            $ids = array_keys($cartItems);
            $products = Product::query()->whereIn('id', $ids)->get()->keyBy('id')->toArray();
        
            $cartItemsWithProducts = [];
            foreach($cartItems as $key => $cartItem){
                if( !isset($products[ $key ] ) ) continue; // skip if product no longer exists         
                $product = $products[ $key ];
                $cartItem['product'] = $product; // add the product in the cart
                $cartItemsWithProducts[] = $cartItem;
            }
        }

        $cartItemsWithProducts = array_reverse($cartItemsWithProducts); // reverse the array to show first the latest added item
        return $cartItemsWithProducts;
    }  
    

    /**
     * Move cart items from the cookie to the database for the specified user.
     * Only items that are not already stored in the database are moved.
     *
     * @param User $user The user whose cart items will be moved to the database.
     * @return void
     */
    public static function moveCartItemsIntoDb(User $user){

            $cartItemsWithProducts = static::getCartItemsWithProducts(null);
            
            // get user's cart items from database
            $dbCartItems = $user->cartItems->keyBy('product_id');
            $cartItems = [];

            foreach($cartItemsWithProducts as $cartItem){

                // skip if cart item already stored in user`s cart items in database
                if( isset( $dbCartItems[ $cartItem['product_id'] ] ) ){
                        continue; 
                }

                // each iteration add cookieCartItem to cart items
                $cartItems[] = [
                    'user_id' => $user->id,
                    'product_id' => $cartItem['product_id'], // id of product in cartItems cookie
                    'quantity' => $cartItem['quantity']
                ];
                
            }

            // return if empty cartItems 
            if(empty($cartItems)) return;

            DB::transaction(function() use($cartItems) {
                CartItem::insert($cartItems);
            });   
            
            
    }
}