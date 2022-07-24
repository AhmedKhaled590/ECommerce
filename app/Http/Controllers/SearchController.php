<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $products = product::where('name', 'like', $search . '%')->limit(10)->get();
        if ($products->count() == 0) {
            $products = product::where('name', 'like', '%' . $search . '%')->limit(10)->get();
        }
        return
        $products->count() > 0 ? response()->json(['message' => 'search successful', 'data' => $products], 200) :
        response()->json(['message' => 'no results found', 'date' => []], 404);
    }
}
