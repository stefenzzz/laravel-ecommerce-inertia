<?php

namespace App\Http\Controllers;

use App\Contracts\CartServiceInterface;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;


/**
 * Class CartItemController
 *
 * Handles cart operations such as adding, removing, and updating products 
 * in the cart for both guest and authenticated users.
 *
 * @package App\Http\Controllers
 */
class CartItemController extends Controller
{
    private CartService $cartService;

    /**
     * Create a new CartItemController instance.
     *
     * @param CartService $cartService The cart service for handling cart operations.
     */
    public function __construct(CartServiceInterface $cartService)
    {
        $this->cartService = $cartService;
    }


    /**
     * Add a product to the cart.
     *
     * @param Request $request  The incoming request containing the product quantity.
     * @param Product $product   The product to add to the cart.
     * @return array             Returns the updated cart count and item details.
     */
    public function add(Request $request, Product $product)
    {
        $quantity = (int) $request->post('quantity', 1);
        if($user = $request->user()){ 
          return $this->cartService->addForUser($product, $quantity, $user);
        } 
        // else not logged in
        return $this->cartService->add($product, $quantity);


    }


     /**
     * Remove a product from the cart.
     *
     * @param Request $request  The incoming request.
     * @param Product $product   The product to remove from the cart.
     * @return array             Returns the updated cart count.
     */
    public function remove(Request $request, Product $product)
    {
        if ($user = $request->user()) {
          return $this->cartService->deleteForUser($product, $user);
        }
        // else not logged in
        return $this->cartService->delete($product);


    }


    /**
     * Update the quantity of a product in the cart.
     *
     * @param Request $request  The incoming request containing the new quantity.
     * @param Product $product   The product to update in the cart.
     * @return array        Returns the updated cart count or null if the quantity is invalid.
     */
    public function update(Request $request, Product $product)
    {   
        $quantity = (int) $request->post('quantity');
        if($quantity <= 0) return; // return if quantity is negative value

        if ($user = $request->user()) {
            return $this->cartService->updateForUser($product, $quantity, $user);
        }
        // else not logged in
        return $this->cartService->update($product, $quantity);

    
    }


}
