<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;


/**
 * Class OrderController
 *
 * This controller handles the order management functionalities, including listing
 * user orders and viewing the details of a specific order.
 *
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{

    /**
     * Display a listing of the user's orders.
     *
     * This method retrieves a paginated list of orders for the authenticated user,
     * along with a count of the items in each order, ordered by creation date.
     *
     * @param Request $request The incoming request object.
     * 
     * @return \Illuminate\Http\Response
     *      Returns the 'Orders' view with a paginated list of orders.
     */
    public function index(Request $request)
    {
        $orders = $request->user()
                    ->orders()
                    ->withCount('items')
                    ->orderBy('created_at','desc')
                    ->paginate(10);
        
        return inertia('Orders', [ 'orders' => $orders ] );
    }


     /**
     * Display the specified order's details.
     *
     * This method retrieves the order along with its items and payment details.
     * It also checks the payment status using Stripe's Checkout Session. If the payment
     * status is confirmed as paid, it updates the order's payment status.
     *
     * @param Order $order The order instance to be displayed.
     * 
     * @return \Illuminate\Http\Response
     *      Returns the 'Order' view with the order and session data, or the 'Checkout' view
     *      with an error message if the session ID is invalid or an exception occurs.
     */
    public static function view(Order $order)
    {
        // load $order->items, $order->items->product, $order->payment
        $order->load(['items.product', 'payment']);
        

        try{
            $session_id = $order->payment->session_id;
            $session = \Stripe\Checkout\Session::retrieve($session_id);

            if (!$session) {
                return inertia('Checkout', ['message' => 'Invalid Session ID']);
            }

            if($session->payment_status === PaymentStatus::Paid->value){
                
                $order->payment->status = PaymentStatus::Paid; 
                $order->payment->update(); // update payment status to paid
      
            }
            $order->items = $order->items->reverse();
            return inertia('Order', compact('order','session'));

        }catch(\Exception $e){

            return inertia('Checkout', ['message' => $e->getMessage()]);

        }
        
    }
}
