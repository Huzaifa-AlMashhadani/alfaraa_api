<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\ProductDetail;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        

        $products = ProductDetail::where("store_id", $id)
        ->with("reviews")
        ->withCount("reviews")
        ->get();

        return $products;


    }

    public function getStoreById($id){

        return Store::find($id);
    }


    public function showAll(){
        return Store::all();
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
    public function store(Request $request)
    {
        //

        $user_id = $request->user_id;
        $user = User::find($user_id);
        if(!$user){
            return response()->json(['message' => 'لا يوجد مستخدم بهذا المعرف '], 404);
        }

        $store = Store::create([
            "name" => $request->name,
            "logo" => "",
            "store_picture" => "",
            "user_id" => $request->user_id
        ]);

        if ($request->hasFile("logo")) {
            $path = $request->file("logo")->store("store_logos", "public");
            $fullUrl = asset("storage/" . $path);
            $store->logo = $fullUrl;
            $store->save();
        }
        if ($request->hasFile("store_picture")) {
            $path = $request->file("store_picture")->store("store_pictures", "public");
            $fullUrl = asset("storage/" . $path);
            $store->store_picture = $fullUrl;
            $store->save();
        }

        return response()->json(['store' => $store], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store, $id)
    {
        //
        $store = Store::findOrFail($id);
        if(!$store){
            return response()->json(['message' => 'المتجر غير موجود '], 404);
        }

        $store->update([
            "name" => $request->name,
            "logo" => $store->logo ?? "",
            "store_picture" => $store->store_picture ?? "",
            "user_id" => $request->user_id
        ]);

        if ($request->hasFile("logo")) {
            $path = $request->file("logo")->store("store_logos", "public");
            $fullUrl = asset("storage/" . $path);
            $store->logo = $fullUrl;
            $store->save();
        }
        if ($request->hasFile("store_picture")) {
            $path = $request->file("store_picture")->store("store_pictures", "public");
            $fullUrl = asset("storage/" . $path);
            $store->store_picture = $fullUrl;
            $store->save();
        }

        return response()->json(['store' => $store], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store, $id)
    {
        $store = Store::findOrFail($id);
        if(!$store){
            return response()->json(['message' => 'المتجر غير موجود '], 404);
        }
        $store->delete();
        return response()->json(['sucsess' => 'Store deleted successfully'], 200);
    }
}
