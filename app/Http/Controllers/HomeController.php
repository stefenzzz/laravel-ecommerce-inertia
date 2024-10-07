<?php

namespace App\Http\Controllers;

use App\Helpers\Cart;
use App\Http\Resources\ProductListResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * Class HomeController
 *
 * This controller handles the homepage functionalities, including listing products
 * and viewing individual product details.
 *
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{

    /**
     * Display a listing of published products.
     *
     * This method retrieves a paginated list of products that are marked as published.
     * If a search query is provided, it filters the products based on the title.
     * 
     * @param Request $request The incoming request object containing the search parameter.
     * 
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *      Returns a paginated list of products in JSON format for API requests,
     *      or renders the 'Home' view with the products for web requests.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('published', '=', 1)
            ->orderBy('created_at', 'desc');


        if($search = $request->search){ // if request incldues search param
            $query->where('title','like','%'.$search.'%');
        }
        
        $products = $query->paginate(12);    

        if($request->wantsJson()) // if request was through fetch, axios or xmlhttprequest
        {
            return $products;
        }
        
        return inertia('Home', compact('products') ); // return Vue Home component page
    }


    /**
     * Display the specified product.
     *
     * This method retrieves a single product and returns the 'Product' view with its details.
     *
     * @param Product $product The product instance to be displayed.
     * 
     * @return \Illuminate\Http\Response
     *      Returns the 'Product' view with the specified product's data.
    */
    public function view(Product $product)
    {
        return inertia('Product', ['product' => $product]);
    }
}
