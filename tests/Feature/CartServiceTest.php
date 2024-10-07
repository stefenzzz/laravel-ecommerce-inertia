<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Helpers\Cart;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use Tests\TestCase;
use Mockery;

class CartServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
    }

    protected function tearDown(): void
    {
        
        Mockery::close(); // Close Mockery after each test
        parent::tearDown();
    }

    // Guest Tests (cookie-based cart)

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_can_add_product_to_cart_as_guest()
    {
        $product = Product::factory()->create(['id' => 1]);

        // Create a mock for the Cart class
        $cartMock = Mockery::mock('alias:' . Cart::class);

        $cartMock->shouldReceive('getCartItemsFromCookie')->once()->andReturn([]);
        $cartMock->shouldReceive('addToCookie')->once();
        $cartMock->shouldReceive('getCount')->once()->andReturn(1);

        $result = $this->cartService->add($product, 1);

        $this->assertEquals(1, $result['count']);
        $this->assertEquals(1, $result['cartItem']['quantity']);
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_can_update_product_quantity_as_guest()
    {
        $product = Product::factory()->create(['id' => 1]);

        $cartMock = Mockery::mock('alias:' . Cart::class);
        $cartMock->shouldReceive('getCartItemsFromCookie')->once()->andReturn([
            1 => ['product_id' => 1, 'quantity' => 1]
        ]);
        $cartMock->shouldReceive('addToCookie')->once();
        $cartMock->shouldReceive('getCount')->once()->andReturn(2);

        $result = $this->cartService->update($product, 2);

        $this->assertEquals(2, $result['count']);
    }

    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function it_can_delete_product_from_cart_as_guest()
    {
        $product = Product::factory()->create(['id' => 1]);

        $cartMock = Mockery::mock('alias:' . Cart::class);
        $cartMock->shouldReceive('getCartItemsFromCookie')->once()->andReturn([
            1 => ['product_id' => 1, 'quantity' => 1]
        ]);
        $cartMock->shouldReceive('addToCookie')->once();
        $cartMock->shouldReceive('getCount')->once()->andReturn(0);

        $result = $this->cartService->delete($product);

        $this->assertEquals(0, $result['count']);
    }

    // Authenticated user tests (database-based cart)

    #[Test]
    public function it_can_add_product_to_cart_for_user()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

        $result = $this->cartService->addForUser($product, 2, $user);

        $this->assertEquals(3, $result['count']);
        $this->assertEquals(3, $result['cartItem']['quantity']);
    }

    #[Test]
    public function it_can_update_product_quantity_for_user()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

        $result = $this->cartService->updateForUser($product, 3, $user);

        $this->assertEquals(3, $result['count']);
    }

    #[Test]
    public function it_can_delete_product_from_cart_for_user()
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $user->cartItems()->create(['product_id' => $product->id, 'quantity' => 1]);

        $result = $this->cartService->deleteForUser($product, $user);

        $this->assertEquals(0, $result['count']);
    }
}
