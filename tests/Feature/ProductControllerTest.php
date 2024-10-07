<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Attributes\Test;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminUserSeeder::class);
        $this->seed(RoleSeeder::class);
    }

    #[Test]
    public function it_can_list_products()
    {   
        $user = User::query()->where('first_name','Admin')->first();
        Product::factory()->count(3)->create();

         // Act: Authenticate the user and send a GET request to the index route
        $response = $this->actingAs($user)->get(route('products.index'));

        // Assert that the Inertia component is rendered correctly
        $response->assertInertia(fn (AssertableInertia $inertia) => $inertia
        ->component('Products') // Make sure to specify the correct Inertia component name
        ->has('products', 3) // Assert that 'products' data has 5 items
        );
    }

    #[Test]
    public function it_can_store_a_new_product_with_image()
    {
        // Arrange: Fake the public storage and create a user
        Storage::fake('public');
        $user = User::query()->where('first_name', 'Admin')->first();
        
        // Act: Send a POST request to store the product
        $response = $this->actingAs($user)->post(route('products.store'), [
            'title' => 'Test Product',
            'description' => 'This is a test product.',
            'price' => 100,
            'image' => UploadedFile::fake()->image('product.jpg'),
            'published' => 0
        ]);
    
        // Assert: Check if there are validation errors
        $response->assertSessionDoesntHaveErrors(); // Ensure there are no validation errors
       

        // Assert: Check the response and product resource data
        $response->assertStatus(200);

        // Assert: Check the database for the product
        $this->assertDatabaseHas('products', [
            'title' => 'Test Product',
            'created_by' => $user->id,
        ]);
    
        // Check if the image was saved in the correct directory
        $product = Product::where('title', 'Test Product')->first(); // Fetch the created product
        $this->assertNotNull($product); // Assert that the product exists
        /** @var Illuminate\Filesystem\FilesystemAdapter */
        $storage = Storage::disk('public');
        $storage->assertExists($product->storage_path);
    }
    
    
    #[Test]
    public function it_can_edit_a_product()
    {
        // Arrange: Create a product
        $product = Product::factory()->create();

        
        $user = User::query()->where('first_name', 'Admin')->first();

        // Act: Send a GET request to edit the product
        $response = $this->actingAs($user)->get(route('products.edit', $product));

        // Assert: Check the response and product resource data
        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => $product->title]);
    }

    #[Test]
    public function it_can_update_a_product()
    {
        // Arrange: Fake storage, create user, and product
        Storage::fake('public');
        $user = User::query()->where('first_name', 'Admin')->first();
        $product = Product::factory()->create();

        // Act: Send a PUT request to update the product
        $response = $this->actingAs($user)->put(route('products.update', $product), [
            'title' => 'Updated Product',
            'price' => 150,
            'published' => 1,
            'image' => UploadedFile::fake()->image('updated.jpg'),
        ]);

        // Assert: Check if there are validation errors
        $response->assertSessionDoesntHaveErrors(); // Ensure there are no validation errors

        // Assert: Check the database and file storage
        $response->assertStatus(200); // check if response status is ok
        $this->assertDatabaseHas('products', ['title' => 'Updated Product']);
        /** @var Illuminate\Filesystem\FilesystemAdapter */
        $storage = Storage::disk('public');
        $storage->assertExists(Product::first()->storage_path);
    }

    #[Test]
    public function it_can_delete_a_product()
    {
        // Arrange: Fake storage and create a product with an image
        Storage::fake('public');
        $product = Product::factory()->create([
            'storage_path' => 'images/test_image.jpg',
        ]);
        $user = User::query()->where('first_name', 'Admin')->first();
        // Act: Send a DELETE request to remove the product
        $this->actingAs($user)->delete(route('products.destroy', $product));

        // Assert: Check that the product's properties are updated but not deleted
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'image' => asset('images/default_product_image.jpg'),
            'price' => 0,
            'published' => false,
            'status' => ProductStatus::Removed,
        ]);

        /** @var Illuminate\Filesystem\FilesystemAdapter */
        $storage = Storage::disk('public');
        $storage->assertMissing('images/test_image.jpg');
    }
}
