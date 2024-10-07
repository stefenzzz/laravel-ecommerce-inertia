<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\AddressType;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Helpers\Cart;
use App\Http\Controllers\CheckoutController;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CountrySeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    #[test]
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(CountrySeeder::class);
        $this->user = User::factory()->create([
            'role_id' => 2
        ]);
    }

    protected function tearDown(): void
    {
        
        Mockery::close(); // Close Mockery after each test
        parent::tearDown();
    }


    #[test]
    public function test_checkout_with_empty_cart()
    {
        $response = $this->actingAs($this->user)->post(route('checkout', [ 'cartItems'=>[] ] ));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('flash_message.error', 'Empty Cart');
    }

    #[test]
    public function test_checkout_user_cart_with_removed_products()
    {
        $product1 = Product::factory()->create([
            'price' => 20,
            'status' => ProductStatus::Removed,
        ]);
        $product2 = Product::factory()->create([
            'price' => 10,
            'status' => ProductStatus::Removed,
        ]);
        $product3 = Product::factory()->create([
            'price' => 30,
            'status' => ProductStatus::Removed,
        ]);

        // Arrange: Create a cart items
        CartItem::Insert([
            [
                'user_id' => $this->user->id,
                'product_id' => $product1->id,
                'quantity' => 2,
            ],
            [
                'user_id' => $this->user->id,
                'product_id' => $product2->id,
                'quantity' => 2,
            ],
            [
                'user_id' => $this->user->id,
                'product_id' => $product3->id,
                'quantity' => 2,
            ],
        ]);


        // Assert: Check that cart items were created for the user
        // Assert: Check that user's cart items contains the inserted cart items
        $this->assertCount(3, $this->user->cartItems);
        $this->assertEquals(2, $this->user->cartItems[0]->quantity);
        $this->assertEquals(2, $this->user->cartItems[1]->quantity);
        $this->assertEquals(2, $this->user->cartItems[2]->quantity);
        $this->assertEquals($product1->id, $this->user->cartItems[0]->product_id);
        $this->assertEquals($product2->id, $this->user->cartItems[1]->product_id);
        $this->assertEquals($product3->id, $this->user->cartItems[2]->product_id);

        $response = $this->actingAs($this->user)->post(route('checkout'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('flash_message.error', 'No items to checkout');
    }

    #[test]
    public function test_checkout_cart_from_cookie_with_removed_products()
    {
        $product1 = Product::factory()->create([
            'price' => 20,
            'status' => ProductStatus::Removed,
        ]);
        $product2 = Product::factory()->create([
            'price' => 10,
            'status' => ProductStatus::Removed,
        ]);
        $product3 = Product::factory()->create([
            'price' => 30,
            'status' => ProductStatus::Removed,
        ]);

        // Arrange: Create a cart items
        $cartItems = [
            $product1->id => [
                'product_id' => $product1->id,
                'quantity' => 2,
            ],
            $product1->id => [
                'product_id' => $product2->id,
                'quantity' => 2,
            ],
            $product1->id => [
                'product_id' => $product3->id,
                'quantity' => 2,
            ],
        ];

        $this->withCookies([
            'cart_items' => json_encode($cartItems)
        ]);


        // Simulate a request to ensure cookies are available in the current session
        $this->get('/'); // Use a home route

        // this test is just following checkoutRegister() method after registering
        // to checkout() method

        $cartItems = Cart::getCartItemsWithProducts(null);
        $cartItems = json_decode( json_encode($cartItems) ); // turn into std class or object


        // Create an instance of the controller
        $controller = new CheckoutController();

        // Call the checkout method directly, passing the $cartItems
        $response = $controller->checkout(request(), $cartItems);

        // Assertions
        $this->assertEquals(route('home'), $response->getTargetUrl());
        
        $this->assertEquals('No items to checkout', session('flash_message.error'));
    }

    #[test]
    public function test_checkout()
    {
        // Arrange: Create products
        $product1 = Product::factory()->create(['price' => 20]);
        $product2 = Product::factory()->create(['price' => 10]);
        $product3 = Product::factory()->create(['price' => 30]);

        // Arrange: Create cart items
        $cartItem1 = CartItem::create(['user_id' => $this->user->id, 'product_id' => $product1->id, 'quantity' => 1,]);
        $cartItem2 = CartItem::create(['user_id' => $this->user->id, 'product_id' => $product2->id, 'quantity' => 1,]);
        $cartItem3 = CartItem::create(['user_id' => $this->user->id, 'product_id' => $product3->id, 'quantity' => 1,]);

        // Calculate total price dynamically
        $totalPrice = $product1->price * $cartItem1->quantity 
                    + $product2->price * $cartItem2->quantity 
                    + $product3->price * $cartItem3->quantity;

        // Act: Perform the checkout
        $response = $this->actingAs($this->user)->post(route('checkout'));

        // Assert: Redirect to Stripe (or another payment gateway)
        $response->assertRedirect(); // Add more specificity if possible

        // Assert: Order and payment are created in the database
        $this->assertDatabaseHas('orders', [
            'total_price' => $totalPrice, // dynamically calculated total price
            'created_by' => $this->user->id,
        ]);

        $this->assertDatabaseHas('payments', [
            'amount' => $totalPrice,
            'status' => PaymentStatus::Pending,
            'created_by' => $this->user->id,
        ]);

        // Iterate each cart item to check if they have been deleted in database
        foreach ([$cartItem1, $cartItem2, $cartItem3] as $cartItem) {
            $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
        }

        // Reload user with cartItems relationship
        $this->user->load('cartItems');

        // Assert: Check that the cartItems are now empty
        $this->assertCount(0, $this->user->cartItems);
    }

    #[test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_exception_message()
    {
        // Arrange: Create a product and add it to the cart
        $product1 = Product::factory()->create(['price' => 20]);
        CartItem::create(['user_id' => $this->user->id, 'product_id' => $product1->id, 'quantity' => 1]);
    
        // Mock Stripe API to throw an exception
        $stripe_session = Mockery::mock('alias:'. \Stripe\Checkout\Session::class);
        $stripe_session->shouldReceive('create')
            ->once()
            ->andThrow(new \Exception('Stripe API error'));
    
        // Act: Perform the checkout
        $response = $this->actingAs($this->user)->post(route('checkout'));
    
        // Assert: Check for the exception page// Assert: Check for the exception page
        $response->assertInertia(fn (AssertableInertia $page) => 
        $page->component('Exception')
            ->has('message')
            ->where('message', 'Stripe API error')
        );

        
    }

    #[test]
    public function test_checkout_failure_and_inertia_output()
    {   
        $sessionId = 'cs_test_b1SYaqLDYXUrd35VyR8sVLmxRr3d9IpqsXO9ip5zJjk16sg5MLUY4t3WPS';
        $response = $this->actingAs($this->user)->get(route('checkout.failure') . '?session_id='.$sessionId );

        $order = Order::create([
            'total_price' => 100,
        ]);

        $product1 = Product::factory()->create(['price' => '20.00']);
        $product2 = Product::factory()->create(['price' => '10.00']);
        $product3 = Product::factory()->create(['price' => '30.00']);

        $payment = Payment::create([
            'order_id' => $order->id,
            'status' => PaymentStatus::Pending,
            'session_id' => $sessionId,
            'type' => 'cc',
            'amount' => 100,
            'created_by' => $this->user->id
        ]);

        $item1 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'unit_price' => $product1->price
        ]);
        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'unit_price' => $product2->price
        ]);
        $item3 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product3->id,
            'quantity' => 1,
            'unit_price' => $product3->price
        ]); 
    
        $items = [
            $item1->load('product')->toArray(),
            $item2->load('product')->toArray(),
            $item3->load('product')->toArray(),
        ];
        
        $response = $this->actingAs($this->user)->get(route('checkout.failure') . '?session_id='.$sessionId );

        $order->load(['items.product', 'payment']);

        // Assert: Check for inerita component props order data output
        $response->assertInertia(fn (AssertableInertia $page) => 
        $page->component('Order')
            ->has('order')
            ->has('session')
            ->where('order.id', $order->id)
            ->where('order.payment.status', $payment->status)
            ->where('order.items', $items)
            ->where('session.payment_status', 'unpaid')
        );

    }


    #[test]
    public function test_checkout_success_and_inertia_output()
    {   
        $sessionId = 'cs_test_b1lrp02Tb9iqFKIbEpSLPt2flHefFiR0Zl3yj3H7Hgs4WmD8pahWS5R6hC';
        $response = $this->actingAs($this->user)->get(route('checkout.failure') . '?session_id='.$sessionId );

        $order = Order::create([
            'total_price' => 100,
        ]);

        $product1 = Product::factory()->create(['price' => '20.00']);
        $product2 = Product::factory()->create(['price' => '10.00']);
        $product3 = Product::factory()->create(['price' => '30.00']);

        $payment = Payment::create([
            'order_id' => $order->id,
            'status' => PaymentStatus::Pending,
            'session_id' => $sessionId,
            'type' => 'cc',
            'amount' => 100,
            'created_by' => $this->user->id
        ]);

        $item1 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'unit_price' => $product1->price
        ]);
        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'unit_price' => $product2->price
        ]);
        $item3 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product3->id,
            'quantity' => 1,
            'unit_price' => $product3->price
        ]); 
    
        $items = [
            $item1->load('product')->toArray(),
            $item2->load('product')->toArray(),
            $item3->load('product')->toArray(),
        ];
        
        $response = $this->actingAs($this->user)->get(route('checkout.success') . '?session_id='.$sessionId );

        $order->load(['items.product', 'payment']);

        // Assert: Check for inerita component props order data output
        $response->assertInertia(fn (AssertableInertia $page) => 
        $page->component('Order')
            ->has('order')
            ->has('session')
            ->where('order.id', $order->id)
            ->where('order.payment.status', PaymentStatus::Paid)
            ->where('order.items', $items)
            ->where('session.payment_status', 'paid')
        );

    }
    

    #[test]
    public function test_checkoutRegister_success()
    {
        // Create valid data for the CheckoutRequest
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'phone' => '1234567890',
            'shippingAddress1' => '123 Main St',
            'shippingAddress2' => '123 Main St',
            'shippingCity' => 'Anytown',
            'shippingState' => 'CA',
            'shippingZipcode' => '90210',
            'shippingCountry' => 'usa',
            'billingAddress1' => '123 Main St',
            'billingAddress2' => '123 Main St',
            'billingCity' => 'Anytown',
            'billingState' => 'CA',
            'billingZipcode' => '90210',
            'billingCountry' => 'usa',
        ];

        
        // Act: Perform checkout registration
        $response = $this->post(route('checkout.register'), $data);

        // Assert: Check if there are validation errors
        $response->assertSessionDoesntHaveErrors(); // Ensure there are no validation errors

        // Assert: Check the response and product resource data
        $response->assertStatus(302);

        // Assert: Ensure the user was created and logged in
        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]);

        // Assert: Ensure the user was created and logged in
        $this->assertDatabaseHas('addresses', [
            'type' => AddressType::Shipping,
            'type' => AddressType::Billing,
            'address1' => '123 Main St',
            'address2' => '123 Main St',
            'city' => 'Anytown',
            'state' => 'CA',
            'zipcode' => '90210',
            'country_code' => 'usa',
        ]);

        $response->assertRedirect(); // Assert redirection to the checkout
    }


    // #[test]
    // public function test_webhook_invalid_signature()
    // {
    //     // Arrange: Create a payload
    //     $payload = json_encode([
    //         'id' => 'test_webhook_session_id',
    //         'payment_status' => 'paid',
    //         'type' => 'checkout.session.completed',
    //     ]);

    //     // Act: Call the webhook endpoint with invalid signature
    //     $response = $this->postJson(route('webhook'), $payload);

    //     // Assert: Ensure a 402 status is returned
    //     $response->assertStatus(402);
    // }


    // #[test]
    // public function test_webhook_successful_payment()
    // {
    //     // Arrange: Create a payment in the database
    //     $payment = Payment::factory()->create([
    //         'status' => PaymentStatus::Pending,
    //         'session_id' => 'test_webhook_session_id',
    //     ]);

    //     // Create the webhook payload
    //     $payload = json_encode([
    //         'id' => 'test_webhook_session_id',
    //         'payment_status' => 'paid',
    //         'type' => 'checkout.session.completed',
    //     ]);

    //     // Act: Call the webhook endpoint
    //     $response = $this->postJson(route('webhook'), $payload, [
    //         'HTTP_STRIPE_SIGNATURE' => 'test_signature',
    //     ]);

    //     // Assert: Ensure payment status was updated
    //     $this->assertDatabaseHas('payments', [
    //         'id' => $payment->id,
    //         'status' => PaymentStatus::Paid,
    //     ]);

    //     $response->assertStatus(200);
    // }
}
