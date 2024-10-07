<?php

namespace Tests\Feature;

use App\Helpers\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Mockery;


class CartHelperTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }


    #[Test]
    public function it_can_get_cart_items_count_for_logged_in_user()
    {

        $user = User::factory()->create();
        $product = Product::factory()->create();
        $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);
        
        $this->assertEquals(2, Cart::getCartItemsCount($user));
    }

    #[Test]
    public function it_can_get_cart_items_count_from_cookie()
    {
        // Set the cookie using withCookies for this session
        $this->withCookies([
            'cart_items' => json_encode([
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 2, 'quantity' => 3]
            ])
        ]);
         // Simulate a request to ensure cookies are available in the current session
        $this->get('/'); // Use a home route

        $this->assertEquals(5, Cart::getCartItemsCount());
    }

    #[Test]
    public function it_can_add_to_cookie()
    {

        // Call the Cart::addToCookie() method to add items to the cookie
        Cart::addToCookie(['product_id' => 1, 'quantity' => 2]);

        $response = $this->get('/');

        // use TestResponse since assert cookie only available in TestResponse
        $response->assertCookie('cart_items', json_encode(['product_id' => 1, 'quantity' => 2]));
    }

    #[Test]
    public function it_can_delete_cookie()
    {
        Cookie::queue('cart_items', json_encode([
            ['product_id' => 1, 'quantity' => 2]
        ]));
        
        Cart::deleteCookie();

        // After the delete action, check the cookies
        $this->assertNull(Cookie::get('cart_items'));
    }

    #[Test]
    public function it_can_get_cart_item_products_for_user()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 2]);
        
        $products = Cart::getCartItemsWithProducts($user);
        
        $this->assertCount(1, $products);
        $this->assertEquals($product->id, $products[0]['product_id']);
    }

    #[Test]
    public function it_can_move_cart_items_into_db()
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        // Set the cookie using withCookies for this session
        // index cart item array using the produt id, as this is how it was set up
        // in inserting cart item in cooke for faster finding the products
        $this->withCookies([
            'cart_items' => json_encode([
               $product1->id => ['product_id' => $product1->id, 'quantity' => 2],
               $product2->id => ['product_id' => $product2->id, 'quantity' => 3]
            ])
        ]);
        // Simulate a request to ensure cookies are available in the current session
        $this->get('/'); // Use a home route

        Cart::moveCartItemsIntoDb($user);

        $user->load('cartItems'); // eager load cartItems of the user
        $this->assertCount(2, $user->cartItems);
    }
}
