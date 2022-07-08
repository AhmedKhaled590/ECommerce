<?php

namespace App\Http\Controllers;

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
        Stripe\Charge::create([
            "amount" => $request->amount * 100,
            "currency" => $request->currency,
            "source" => $token,
            "description" => "Test payment from Khold",
        ]);

        Session::flash('success', 'Payment successful!');

        return response()->json(['message' => 'Payment successful!']);
    }
}
