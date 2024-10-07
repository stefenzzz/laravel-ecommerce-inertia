<?php

namespace App\Contracts;

use App\Models\Product;
use App\Models\User;

/**
 * Interface CartServiceInterface
 *
 * Defines the contract for cart operations for both guest and authenticated users.
 * Implementing classes must provide methods to add, update, and delete products 
 * from the cart, handling both guest and user-specific operations.
 *
 * @package App\Contracts
 */
interface CartServiceInterface
{
    // Common methods for guest users
    public function add(Product $product, int $quantity);
    public function update(Product $product, int $quantity);
    public function delete(Product $product);
    // Authenticated user-specific methods
    public function addForUser(Product $product, int $quantity, User $user);
    public function updateForUser(Product $product, int $quantity, User $user);
    public function deleteForUser(Product $product, User $user);
}
