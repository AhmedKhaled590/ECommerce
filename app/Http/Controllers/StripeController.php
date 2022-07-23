<?php

namespace App\Http\Controllers;

use App\Models\cart;
use App\Models\transactions;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Http\Request;
use Session;
use Stripe;

class StripeController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return 'stripe';
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        //create stripe token for payment using Stripe
        try {
            $stripe = new \Stripe\StripeClient(
                env('STRIPE_SECRET')
            );
            $token = $stripe->tokens->create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $cart = cart::where('user_id', auth()->user()->id)->get();
            $total = 0;
            foreach ($cart as $item) {
                $total += $item->price_per_quantity;
            }
            $response = Stripe\Charge::create([
                "amount" => $total * 100,
                "currency" => $request->currency,
                "source" => $token,
                "description" => "Test payment from Khold",
            ]);
            $transaction = transactions::create([
                'stripe_id' => $response->id,
                'user_id' => auth()->user()->id,
                'cart_id' => $cart[0]->id,
                'amount' => $total,
                'currency' => $request->currency,
                'payment_method_id' => $response->payment_method,
                'payment_method_type' => $response->payment_method_details->card->brand,
                'status' => $response->status,
            ]);
            auth()->user()->notify(new PaymentSuccessNotification());
            Session::flash('success', 'Payment successful!');

            return response()->json(['message' => 'Payment successful!', 'transaction' => $transaction]);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
