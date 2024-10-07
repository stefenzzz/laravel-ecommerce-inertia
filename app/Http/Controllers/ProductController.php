<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/**
 * Class ProductController
 *
 * This controller handles the CRUD operations for products, including
 * listing, creating, editing, updating, and deleting products.
 *
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{



    /**
     * Display a paginated list of products.
     *
     * This method retrieves a list of products based on optional search,
     * sorting, and pagination parameters from the request.
     *
     * @param Request $request The incoming request object.
     * 
     * @return \Illuminate\Http\Response
     *      Returns the 'Products' view with a paginated list of products.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        $query = Product::query()
            ->where('title', 'like', "%{$search}%")
            ->where('status', ProductStatus::Active)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    
        $products =  ProductListResource::collection($query);
        return inertia('Products', compact('products'));
    }



    /**
     * Store a newly created product in the database.
     *
     * This method validates the incoming request data and creates a new
     * product entry. It also handles the image upload and storage.
     *
     * @param ProductRequest $request The validated product request object.
     * 
     * @return \Illuminate\Http\Response
     *      Returns a response indicating the result of the product creation.
     */
    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        if( isset($data['image']) ){
            $image = $data['image'];
            [ $url, $path ] = $this->saveImage( $image );
            $data['image'] = $url;
            $data['storage_path'] = $path;
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();
        }
     
        $product = Product::create($data);
    }



    /**
     * Show the form for editing a specified product.
     *
     * This method retrieves a product for editing and returns its resource.
     *
     * @param Request $request The incoming request object.
     * @param Product $product The product instance to be edited.
     * 
     * @return ProductResource
     *      Returns the resource for the specified product.
     */
    public function edit(Request $request, Product $product)
    {
        return new ProductResource($product);
    }



    /**
     * Update the specified product in the database.
     *
     * This method validates the incoming request data and updates the
     * specified product. It handles image uploads and storage as well.
     *
     * @param ProductRequest $request The validated product request object.
     * @param Product $product The product instance to be updated.
     * 
     * @return \Illuminate\Http\Response
     *      Returns a response indicating the result of the product update.
     */
    public function update(ProductRequest $request, Product $product)
    {
        
        $data = $request->validated();
        $data['updated_by'] = $request->user()->id;

        if( isset($data['image']) ){ 
            $image = $data['image'];
            [ $url, $path ] = $this->saveImage( $image );
            $data['image'] = $url;
            $data['storage_path'] = $path;
            $data['image_mime'] = $image->getClientMimeType();
            $data['image_size'] = $image->getSize();

            if($product->storage_path){
                Storage::disk('public')->deleteDirectory( dirname($product->storage_path )); // delete current image in storage path if exists
            } 
        }else{
            unset($data['image']); // dont include data['image'] on update if its not set or no request to update the image
        }
        
        $product->update($data);
    }



    /**
     * Mark a product as removed and clean up its associated files.
     *
     * This method deletes the storage directory for the product if it exists,
     * resets the product's image to a default placeholder, sets the price to zero,
     * marks the product as unpublished, and updates its status to "Removed."
     * Note: This does not perform a soft delete; it simply changes the product's status.
     *
     * @return \Illuminate\Http\Response
     *      Returns a response indicating the result of the product deletion.
     */
    public function destroy(Product $product)
    {
        if($product->storage_path){
            Storage::disk('public')->deleteDirectory( dirname($product->storage_path ));
        }
        
        $product->image = asset('images/default_product_image.jpg');
        $product->price = 0;
        $product->published = false;
        $product->status = ProductStatus::Removed;
        $product->save();

    }


    /**
     * Save the uploaded image and return its URL and path.
     *
     * This method handles storing the uploaded image to a designated
     * directory and returns the public URL and storage path of the saved image.
     *
     * @param UploadedFile $image The uploaded image file.
     * 
     * @return array
     *      An array containing the public URL and storage path of the saved image.
     */
    public function saveImage(UploadedFile $image)
    {   
        $directory = 'images/'. Str::random();
        if( !Storage::disk('public')->exists($directory) ){    
            Storage::disk('public')->makeDirectory($directory);
        }
        $fileName = $image->getClientOriginalName();
        Storage::disk('public')->putFileAs($directory, $image, $fileName);
        $path = $directory.'/'.$fileName;

        /** @var Illuminate\Filesystem\FilesystemAdapter*/
        $publicStorage =  Storage::disk('public');
        $url = $publicStorage->url($path);
        return [$url, $path];
    }
}
