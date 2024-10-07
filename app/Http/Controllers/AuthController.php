<?php

namespace App\Http\Controllers;

use App\Helpers\Cart;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthController
 *
 * Handles user authentication operations including registration, login, and logout.
 * It manages user registration by creating a new user, logging them in, and sending
 * an email verification notification. It also handles user login and logout, 
 * managing session and flash messages for user actions.
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Register user
     *
     * @param RegisterRequest $request
     * @return void
     */
    public function register(RegisterRequest $request)
    {

        $formFields = $request->validated();
        $user =  User::create($formFields);
        Auth::login($user);
        $user->sendEmailVerificationNotification(); // send email for verification
        Cart::moveCartItemsIntoDb($user); // trasnfer cart items to database
        $request->session()->regenerate();

        return redirect()->intended('/profile')
        ->withCookie(Cookie::forget('cart_items')); // delete cart items in cookie

    }

    /**
     * Login user
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        
        $formFields = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ]);

        $user = User::where('email',$formFields['email'])->first();

        if($user && Hash::check($formFields['password'],$user->password) ){
            Auth::login($user);        
            $request->session()->regenerate();
            return redirect()->intended('/')->with('flash_message', [ 'success' => 'You have successfully logged in' ]);
        }
        return back()->withErrors(['password' => 'Credentials are incorrect'])->onlyInput('password');        
    }

    /**
     * Logout user with flash message
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerate();
        
        return redirect()->route('login')->with('flash_message', [ 'success' => 'You have logged out' ] );
    }



}
