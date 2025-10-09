<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriesRequest;
use App\Http\Requests\UpdateCategoriesRequest;
use App\Models\Categories;
use App\Models\ProductDetail;
use Database\Seeders\ProductDetailSeeder;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Categories::all();
    }

    // get Categorie By Id 

    public function getCategorieById ($id){
        $categorie = Categories::where("id", $id)->get();

        return  $categorie;
    }

    // get products by categorie 
    public function getPorducts($id){
        $products = ProductDetail::where("categories_id", $id)->get();

        return $products;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categorie = Categories::create([
            "name" => $request->name,
            "description" => $request->description,
            "ad_imae_url" => ""
        ]);
        if ($request->hasFile("ad_image")) {
            $path = $request->file("ad_image")->store("categories_images", "public");
            $fullUrl = asset("storage/" . $path);
            $categorie->ad_imae_url = $fullUrl;
            $categorie->save();
        }

        return response()->json(['message' => 'categorie created successfully', 'categorie' => $categorie], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoriesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Categories $categories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categories $categories, $id)
    {
        //
        $categorie = Categories::findOrFail($id);

        $categorie->update([
            "name" => $request->name,
            "description" => $request->description,
            "ad_imae_url" => ""
        ]);
        if ($request->hasFile("ad_image")) {
            $path = $request->file("ad_image")->store("categories_images", "public");
            $fullUrl = asset("storage/" . $path);
            $categorie->ad_imae_url = $fullUrl;
            $categorie->save();
        }
        return response()->json(['message' => 'categorie updated successfully', 'categorie' => $categorie], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categories $categories, $id)
    {
        $categorie = Categories::findOrFail($id);

        $categorie->delete();

        return response(["message" => "deleted !"], 200);
    }
}
