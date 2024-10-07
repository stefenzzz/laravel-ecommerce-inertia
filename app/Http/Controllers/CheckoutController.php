<?php

namespace App\Http\Controllers;

use App\Enums\AddressType;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Helpers\Cart;
use App\Http\Requests\CheckoutRequest;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



/**
 * Class CheckoutController
 *
 * Handles the checkout process for users, including payment processing,
 * order creation, and user registration.
 *
 * @package App\Http\Controllers
 */
class CheckoutController extends Controller
{


    /**
     * Process the checkout with the given cart items.
     *
     * If no cart items are provided, it fetches the user's cart items.
     * Excludes removed products, Calculates the total price and prepares the checkout session with Stripe.
     * Stores the order and payment information in the database.
     *
     * @param Request $request The HTTP request instance.
     * @param array $cartItems An optional array of cart items.
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function checkout(Request $request, array $cartItems = [])
    {
        $user = $request->user();
        if(empty($cartItems)){ // check cartItems from parameter if empty        
          $cartItems = $user->cartItems()->with('product')->get();
          
          if($cartItems->isEmpty()){ // check if user->cartItems is empty
            return redirect()->route('home')->with('flash_message', ['error' => 'Empty Cart'] );
          }

        }
          
        $orderItems = [];
        $lineItems = [];
        $totalPrice = 0;
        
        foreach($cartItems as $cartItem){
            
            if($cartItem->product->status === ProductStatus::Removed->value // evalutes status as string if product is from cookie
             || $cartItem->product->status === ProductStatus::Removed  // evalutes status as enum if product is eloquent model
             ) continue; // skip if product is removed.

            $totalPrice += (int) $cartItem->quantity * (float) $cartItem->product->price;
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $cartItem->product->title,
                    ],
                    'unit_amount' => $cartItem->product->price * 100,
                ],
                'quantity' => $cartItem->quantity,
            ];
            $orderItems[] = [
                'product_id' => $cartItem->product->id,
                'quantity' => $cartItem->quantity,
                'unit_price' => $cartItem->product->price
            ];
        }
        // return if no items to checkout
        if(empty($lineItems)) return redirect()->route('home')->with('flash_message',['error' => 'No items to checkout']);

        try {
            // Stripe API key is set in \Provider\AppServiceProvider->boot()
            $session = \Stripe\Checkout\Session::create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('checkout.success', [], true) .'?session_id= {CHECKOUT_SESSION_ID} ',
                'cancel_url' => route('checkout.failure', [], true) .'?session_id= {CHECKOUT_SESSION_ID} ',
            ]);

            // store order and payments in database , if failed roll back and redirect to checkout page
            DB::transaction(function () use ($user, $session, $orderItems, $totalPrice) {
                // Create Order
                $order = Order::create([
                    'total_price' => $totalPrice,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);

                // set order items with order_id
                foreach ($orderItems as &$orderItem) {
                    $orderItem['order_id'] = $order->id;
                }

                // Bulk insert order items
                OrderItem::insert($orderItems);

                // Create Payment
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $totalPrice,
                    'status' => PaymentStatus::Pending,
                    'type' => 'cc',
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                    'session_id' => $session->id
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Transaction failed: ' . $e->getMessage());        
            return inertia('Exception', ['message' => $e->getMessage() ]);
        }
     
     // delete cartItems
     $user->cartItems()->delete();
     return Inertia::location($session->url); // external link to strip checkout

    }

    /**
     * Handle a failed checkout session.
     *
     * Retrieves the checkout session and the associated payment information.
     * Displays the order details if available.
     *
     * @param Request $request The HTTP request instance.
     * @return \Inertia\Response
     */
    public function failure(Request $request)
    {     
        try{
            $user = $request->user();
            $session_id = $request->session_id;
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if (!$session) {
                return inertia('Exception', ['message' => 'Invalid Session ID']);
            }

            $payment = $user->payments()->where(['session_id' => $session_id])->first();
            
            if(!$payment){
                throw new NotFoundHttpException(); // displays 404 not found
            }

            // load $order->items, $order->items->product, $order->payment
            $order = $payment->order;
            $order->load(['items.product', 'payment']);

            return inertia('Order', compact('order','session'));
            
        }catch(\Exception $e){

            return inertia('Exception', ['message' => $e->getMessage()]);

        }


    }


    /**
     * Handle a successful checkout session.
     *
     * Verifies the checkout session and updates the payment status if paid.
     * Displays the order details if available.
     *
     * @param Request $request The HTTP request instance.
     * @return \Inertia\Response
     */
    public function success(Request $request)
    {       
        try{
            $user = $request->user();
            $session_id = $request->session_id;
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if (!$session) {
                return inertia('Exception', ['message' => 'Invalid Session ID']);
            }

            $payment = $user->payments()->where(['session_id' => $session_id])
            ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
            ->first();
            
            if(!$payment){
                throw new NotFoundHttpException(); // displays 404 not found
            }
            if($session->payment_status === PaymentStatus::Paid->value 
                && $payment->status === PaymentStatus::Pending)
            {               
                $payment->status = PaymentStatus::Paid; 
                $payment->update(); // update payment status to paid
            }
            
            // load $order->items, $order->items->product, $order->payment
            $order = $payment->order;
            $order->load(['items.product', 'payment']);

            return inertia('Order', compact('order','session'));

        }catch(\Exception $e){

            return inertia('Exception', ['message' => $e->getMessage()]);

        }
        
    }

    /**
     * Checkout an existing order.
     *
     * Retrieves the session information for the specified order and
     * verifies the payment status.
     *
     * @param Request $request The HTTP request instance.
     * @param Order $order The order to be checked out.
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function checkoutOrder(Request $request, Order $order)
    {
        try{
            $session_id = $order->payment->session_id;
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if (!$session) {
                return inertia('Exception', ['message' => 'Invalid Session ID']);
            }

            if($session->payment_status === PaymentStatus::Paid->value){
                
                $order->payment->status = PaymentStatus::Paid; 
                $order->payment->update(); // update payment status to paid

                // load $order->items, $order->items->product, $order->payment
                $order->load(['items.product', 'payment']);
                return inertia('Order', compact('session', 'order') );
            }
            
            return Inertia::location($session->url);

        }catch(\Exception $e){

            return inertia('Exception', ['message' => $e->getMessage()]);

        }

    }


    /**
     * Webhook endpoint for Stripe events.
     *
     * Handles incoming webhook events from Stripe and updates payment statuses
     * accordingly.
     *
     * @return \Illuminate\Http\Response
     */
    public function webhook()
    {   
        $endpoint_secret = env('WEBHOOK_SECRET_KEY');

        $payload = @file_get_contents('php://input');
        $signature_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $signature_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid PayLoad', 401); // Invalid payload
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid Response', 402); // Invalid signature
        }
        // Handle the events
        switch ($event->type) { 

            case 'checkout.session.completed':
                $paymentIntent = $event->data->object;         
                $sessionId = $paymentIntent['id'];
                $payment_status = $paymentIntent['payment_status'];
        
                if($payment_status === 'paid'){

                    $payment = Payment::query()
                    ->where(['session_id' => $sessionId, 'status' => PaymentStatus::Pending])
                    ->first();

                    if ($payment) {
                        $payment->status = PaymentStatus::Paid; 
                        $payment->update(); // update payment status to paid
                    }
                }else{
                    return response('Payment Required', 402);
                }
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('', 200);
    }


    /**
     * Register a new user during the checkout process.
     *
     * Validates the checkout request, creates a new user and their
     * associated addresses, and logs the user in.
     *
     * @param CheckoutRequest $request The validated checkout request.
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function checkoutRegister(CheckoutRequest $request)
    {   
       
        $data = $request->validated();

        $account = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'],
        ];

        $shippingAddress = [
            'type' => AddressType::Shipping,
            'address1' => $data['shippingAddress1'],
            'address2' => $data['shippingAddress2'],
            'city' => $data['shippingCity'],
            'state' => $data['shippingState'],
            'zipcode' => $data['shippingZipcode'],
            'country_code' => $data['shippingCountry'],
        ];

        $billingAddress = [
            'type' => AddressType::Billing,
            'address1' => $data['billingAddress1'],
            'address2' => $data['billingAddress2'],
            'city' => $data['billingCity'],
            'state' => $data['billingState'],
            'zipcode' => $data['billingZipcode'],
            'country_code' => $data['billingCountry'],
        ];

    
        try{
            DB::transaction(function() use($account, $shippingAddress, $billingAddress){
                $user = User::create($account);
                $user->shippingAddress()->create($shippingAddress);
                $user->billingAddress()->create($billingAddress);
                Auth::login($user); // login the user
            });
        }catch(\Exception $e){
            Log::error('Checkout Registration Failed: ' . $e->getMessage());        
            return inertia('Exception', ['message' => $e->getMessage() ]);
        }

        $cartItems = Cart::getCartItemsWithProducts(null);
        $cartItems = json_decode( json_encode($cartItems) ); // turn into std class or object
        
        Cart::deleteCookie('cart_items'); // delete cart items in cookie

        $request->session()->regenerate(); // regenerate session since user logged in

      return $this->checkout($request, $cartItems);

        
    }

    /**
     * Display the checkout form with available countries.
     *
     * @return \Inertia\Response
    */
    public function checkoutForm()
    {
        $countries = Country::query()->orderBy('name')->get()->toArray();

        return inertia('CheckoutForm',[ 'countries' => $countries ]);

    }

}
