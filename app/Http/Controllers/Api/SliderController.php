<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\ProductDetail;


class SliderController extends Controller
{
    //
    public function index(){
       $products = ProductDetail::take(25)
       ->get();
        return [
            "title" => "افضل المنتجات ",
            "description" => "افضل المنتجات ",
            "cardsData" => $products
        ];
    }
    public function slider($id){
        
    $products = ProductDetail::where("categories_id", $id)
    ->take(25)
    ->with("reviews")
    ->withCount("reviews")
    ->get();
    $categorie = Categories::find($id);

    return [
    "title" => $categorie->name,
    "description" => $categorie->description,
    "cardsData" => $products
    ];
        
    
       
    }
}
