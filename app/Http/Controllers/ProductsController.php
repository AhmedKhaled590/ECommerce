<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return product::paginate(4);
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
            $body = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'price' => 'required|numeric',
                'category_id' => 'required|numeric',
                'images' => 'required',
                'currency' => 'required|string|max:3',
            ]);
            $path = $request->file('images')->storeAs('images', $request->file('images')->getClientOriginalName());
            $body['images'] = $path;
            $product = product::create($body);
            return response()->json(['message' => 'product created successfully', 'product' => $product], 201);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = product::find($id);
        if (!$product) {
            return response()->json(['message' => 'product not found'], 404);
        }
        return response()->json(['message' => 'product found successfully', 'product' => $product], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $product = product::find($id);
            if (!$product) {
                return response()->json(['message' => 'product not found'], 404);
            }
            $body = $request->validate([
                'name' => 'string|max:255',
                'description' => 'string|max:255',
                'price' => 'numeric',
                'category_id' => 'numeric',
                'images' => 'string',
                'currency' => 'string|max:3',
                'quantity_available' => 'numeric',
                'review' => 'string|max:255',
            ]);
            $path = $request->file('images')->storeAs('images', $request->file('images')->getClientOriginalName());
            $body['images'] = $path;
            $product->update($body);
            return response()->json(['message' => 'product updated successfully', 'product' => $product], 200);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = product::find($id);
            if (!$product) {
                return response()->json(['message' => 'product not found'], 404);
            }
            $product->delete();
            return response()->json(['message' => 'product deleted successfully'], 200);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addMultipleProducts(Request $request)
    {
        try {
            $body = $request->validate([
                'products' => 'required|array',
            ]);
            $products = $body['products'];
            foreach ($products as $product) {

                $product = product::create($product);
            }
            return response()->json(['message' => 'products added successfully', 'products' => $products], 201);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getCategory($id)
    {
        $product = product::find($id);
        if (!$product) {
            return response()->json(['message' => 'product not found'], 404);
        }
        return response()->json(['message' => 'product found successfully', 'category' => $product->categories], 200);
    }
}
