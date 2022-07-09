<?php

namespace App\Http\Controllers;

use App\Models\cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *  @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $cart = DB::table('carts')
                ->join('products', 'carts.product_id', '=', 'products.id')
                ->join('users', 'carts.user_id', '=', 'users.id')
                ->select('carts.*', 'products.name', 'products.price', 'users.name as user_name')
                ->get();
            return response()->json(['message' => 'cart retrieved successfully', 'cart' => $cart], 200);
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
                ->where('user_id', $request->user_id)
                ->count();
            $body = $request->validate([
                'product_id' => 'required|numeric',
                'quantity' => 'required|numeric',
            ]);
            $body['price_per_quantity'] = 0;
            $productId = $body['product_id'];
            if ($cart = cart::where('user_id', auth()->user()->id)->where('product_id', $productId)->first()) {
                $cart->increment('quantity', $body['quantity']);
            } else {
                $cart = cart::create($body);
            }
            $cart_size_after_add = DB::table('carts')
                ->where('user_id', $request->user_id)
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
