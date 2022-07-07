<?php

namespace App\Http\Controllers;

use App\Models\category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return category::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $body = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category = category::create($body);
        return response()->json(['message' => 'category created successfully', 'category' => $category], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = category::find($id);
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }
        return response()->json(['message' => 'category found successfully', 'category' => $category], 200);
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
        $category = category::find($id);
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }
        $body = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category->update($body);
        return response()->json(['message' => 'category updated successfully', 'category' => $category], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = category::find($id);
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'category deleted successfully'], 200);
    }
    /**
     * return the products of a category
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProducts($id)
    {
        $category = category::find($id);
        if (!$category) {
            return response()->json(['message' => 'category not found'], 404);
        }
        return $category->products;
    }

    public function addMultipleCategories(Request $request)
    {
        try {
            $categories = $request->validate([
                'categories' => 'required|array',
            ]);

            $categories = $categories['categories'];
            category::insert($categories);
            return response()->json(['message' => 'categories added successfully'], 200);
        } catch (\Exception$e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
