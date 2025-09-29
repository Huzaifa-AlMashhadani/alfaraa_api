<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductDetailRequest;
use App\Http\Requests\UpdateProductDetailRequest;
use App\Models\ProductCompatibility;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProductDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // ProductController.php
    public function search(Request $request)
    {
        $query = $request->input('q'); 

        $results = ProductDetail::where('ar_name', 'LIKE', "%{$query}%")
                    ->orWhere('en_name', 'LIKE', "%{$query}%")
                    ->with("reviews")
                    ->withCount("reviews")
                    ->get();

        return response()->json($results);
    }
    public function searchCompany(Request $request)
    {
        $query = $request->input('company'); 

        $product_ids = ProductCompatibility::where('brand_id', $query)->pluck("product_detail_id");

        $porducts = ProductDetail::whereIn("id",$product_ids)
        ->with("reviews")
        ->withCount("reviews")
        ->get();
        return response()->json($porducts);
    }

    public function index($id)
    {
        return ProductDetail::find($id);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductDetailRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */


public function show($id)
{
    $product = ProductDetail::with([
        'reviews',
        'productUnits.products', 
        'part_number',
        'images'
    ])
    ->withCount('reviews')
    ->find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    return response()->json($product);
}






    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductDetail $productDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductDetailRequest $request, ProductDetail $productDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductDetail $productDetail)
    {
        //
    }
}
