<?php

use App\Http\Controllers\AdvertisementsController;
use App\Http\Controllers\Api\ArticlesController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DataSearchController;
use App\Http\Controllers\Api\GarageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductCompatibilityController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StoreController;
use App\Models\ProductCompatibility;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/edit-account', [AuthController::class, 'edit']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', function(Request $request) {
        return $request->user();
    });
});

// search data : brand and module and date and engine 
Route::get('/searchData', [DataSearchController::class, 'index']);

// get products by Company
Route::get("/searchCompany", [ProductDetailController::class, "searchCompany"]);

// return data for promt search 
Route::get('/search', [ProductCompatibilityController::class, 'search']);

// Product ompatibility 
Route::middleware('auth:sanctum')->get('/products/{id}/check-compatibility', 
    [ProductCompatibilityController::class, 'checkCompatibilityForGarage']
);
// routes/api.php
Route::get('/products/search', [ProductDetailController::class, 'search']);


// slider data like title and subtitle and some products 
Route::get('/sliderData', [SliderController::class, "index"]);

// show product detiles 
Route::get('/product/{id}',[ProductDetailController::class, 'show']);

// get garage cars 
Route::middleware('auth:sanctum')->get("/garage", [GarageController::class, "index"]);

// create car in garage 
Route::middleware('auth:sanctum')->post("/createCarGrage", [GarageController::class, "store"]);

//delete car from garage
Route::middleware('auth:sanctum')->delete('/garage/{garage}', [GarageController::class, 'destroy']);

// get Favorite porducts 
Route::middleware("auth:sanctum")->get("/favorites", [FavoriteController::class, "index"]);

// Create Favorite porducts 
Route::middleware("auth:sanctum")->post("/favorites/{product_id}", [FavoriteController::class, "store"]);

// Create Favorite porducts 
Route::middleware("auth:sanctum")->delete("/favorites/{product_id}", [FavoriteController::class, "destroy"]);

// get all catigorie or somthing 
Route::get('/allcategoire', [CategoriesController::class, "index"]);

// get categorie by id 
Route::get('/getCategorieById/{id}', [CategoriesController::class, "getCategorieById"]);

// get products by categorie 
Route::get("/getPorducts/{id}", [CategoriesController::class, "getPorducts"]);

// get products by categorie 
Route::get("/slider/{id}", [SliderController::class, "slider"]);

// get Articles from home page 3 Articles
Route::get("/articles", [ArticlesController::class, "index"]);

// get Articles by id
Route::get("/articles/{id}", [ArticlesController::class, "show"]);

// get product with reviews and units 
Route::get("/productID/{id}", [ProductDetailController::class, "show"]);

// check for make order 
Route::middleware("auth:sanctum")->post("/checkOrder", [OrderController::class,"index"]);

// get cart for user 
Route::middleware("auth:sanctum")->get("/getCart", [CartController::class, "index"]);

// add to cart 
Route::middleware("auth:sanctum")->post("/addToCart", [CartController::class, "store"]);

// edit cart 
Route::middleware("auth:sanctum")->post("/editCart", [CartController::class, "edit"]);

// delete cart 
Route::middleware("auth:sanctum")->post("/deleteCart", [CartController::class, "destroy"]);


// get shop data 
Route::get("/shopData", [ShopController::class, "section"]);

// get shop data 
Route::get("/allSections", [ShopController::class, "allSections"]);

// get shop data 
Route::get("/diamond_ad", [AdvertisementsController::class, "index"]);


// get shop data 
Route::get("/brands", [BrandsController::class, "index"]);

// get all brands  
Route::get("/showAllBrands", [BrandsController::class, "showAllBrands"]);


// get stroe Products 
Route::get("/store/{id}", [StoreController::class, "index"]);

// get stroe data by id 
Route::get("/getStoreById/{id}", [StoreController::class, "getStoreById"]);

// get All Stores 
Route::get("/getAllStores", [StoreController::class, "showAll"]);