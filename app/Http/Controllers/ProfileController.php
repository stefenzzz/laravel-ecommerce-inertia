<?php

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Http\Requests\ProfileRequest;
use App\Models\Country;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class ProfileController
 * Handles user profile-related actions including viewing and updating profile information.
 */
class ProfileController extends Controller
{

    /**
     * Display the user's profile information along with available countries for selection.
     *
     * @param Request $request The incoming request instance.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $countries = Country::query()->orderBy('name')->get()->toArray();
        $user = $request->user();
        $billing = $user->billingAddress ?? [];
        $shipping = $user->shippingAddress ?? [];

        return inertia('Profile', compact('countries', 'user', 'shipping','billing') );
        
    }
    
    /**
     * Update the user's profile information, including shipping and billing addresses.
     *
     * @param ProfileRequest $request The validated request containing user profile data.
     * @param User $user The user instance to update.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request, User $user)
    {
        $data = $request->validated();
        $shipping = $data['shipping'];
        $billing = $data['billing'];

        
        // unset billing and shipping associative array so user details will be remain
        unset($data['shipping'],$data['billing']);

        $userForm = $data;
        $user = $request->user();
        $shipping['type'] = AddressType::Shipping;
        $billing['type'] = AddressType::Billing;

        $message = '';
        try{

            DB::transaction(function() use($user, $userForm, $shipping, $billing, &$message){
                
                $user->fill($userForm);
                if($user->isDirty()){
                    $user->save(); // update
                    $message .= 'Updated Basic Information.<br>';
                }
         
                if($user->shippingAddress){ 
                    $user->shippingAddress->fill($shipping); 
                    if($user->shippingAddress->isDirty()){ 
                        $user->shippingAddress->save(); // update
                        $message .= 'Updated Shipping Information. <br>';
                    }
                }else{                  
                    $user->shippingAddress()->create($shipping); // create
                    $message = 'Updated Shipping Information.<br>';
                }
        
                if($user->billingAddress){ 
                    $user->billingAddress->fill($billing); 
                    if($user->billingAddress->isDirty()){ 
                        $user->billingAddress->save(); // update
                        $message .= 'Updated Billing Information.<br>';
                    }
                }else{
                    $user->billingAddress()->create($billing); // create
                    $message .= 'Updated Billing Information.<br>';
                }

                
            });
        }catch(Exception $e){
            return inertia('Exception', ['message' => $e->getMessage()]);
        }

      return back()->with('flash_message', [ 'success' => $message ?: 'No changes were made.' ] );
    }
}
