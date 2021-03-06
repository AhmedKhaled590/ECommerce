<?php

namespace App\Http\Controllers;

use App\Models\cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *  @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            Log::debug('CartController::index', ['user_id' => auth()->user()->id]);
            $cart = cart::with('products')->where('user_id', auth()->user()->id)->get();
            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += $item->price_per_quantity;
            }
            $response = [
                'total_price' => $totalPrice,
                'data' => $cart,
            ];
            return response()->json(['message' => 'cart retrieved successfully', 'resonse' => $response], 200);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $cart_size = DB::table('carts')
                ->where('user_id', auth()->user()->id)
                ->count();
            $body = $request->validate([
                'product_id' => 'required|numeric',
                'quantity' => 'required|numeric',
            ]);
            $body['price_per_quantity'] = 0;
            $productId = $body['product_id'];

            if ($cart = cart::where('user_id', auth()->user()->id)->where('product_id', $productId)->first()) {
                $cart->products->quantity_available -= $body['quantity'];
                $cart->increment('quantity', $body['quantity']);
                $cart->save();
                return response()->json(['message' => 'product added to cart successfully', 'cart' => $cart], 201);
            } else {
                $cart = cart::create($body);
                $cart->products->quantity_available -= $body['quantity'];
                $cart->products->save();
            }
            $cart_size_after_add = cart::with('products', 'users')
                ->where('user_id', auth()->user()->id)
                ->count();
            if ($cart_size_after_add > $cart_size) {
                return response()->json(['message' => 'product added to cart successfully', 'cart' => $cart], 201);
            } else {
                return response()->json(['message' => "sorry,we can't provide this quantity."], 400);
            }

        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function decreaseQuantity(Request $request)
    {
        try {
            $body = $request->validate([
                'product_id' => 'required|numeric',
                'quantity' => 'required|numeric',
            ]);
            $productId = $body['product_id'];
            if ($cart = cart::where('user_id', auth()->user()->id)->where('product_id', $productId)->first()) {
                $cart->decrement('quantity', $body['quantity']);
                if ($cart->quantity == 0) {
                    $cart->delete();
                    return response()->json(['message' => 'product removed from cart successfully'], 200);
                }
            } else {
                return response()->json(['message' => 'product not found in cart'], 404);
            }
            return response()->json(['message' => 'product quantity decreased successfully', 'cart' => $cart], 200);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

}
