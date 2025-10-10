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
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\EngineeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ModuleDateController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\ProductCompatibilityController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UnitController;
use App\Mail\SendMessageMail;
use App\Models\Articles;
use App\Models\ModuleDate;
use App\Models\ProductCompatibility;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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


// get all messages
Route::get("/messages", [MessageController::class, "index"]);
// create a new tiket
Route::post("/createTiket", [ConversationController::class, "store"]);

// create a new message
Route::post("/createMessage", [MessageController::class, "addMessage"]);

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

// get all Modules  
Route::get("/showAllModules", [ModuleController::class, "index"]);

// get all Modules date 
Route::get("/showAllModulesDate", [ModuleDateController::class, "index"]);


// get stroe Products 
Route::get("/store/{id}", [StoreController::class, "index"]);

// get stroe data by id 
Route::get("/getStoreById/{id}", [StoreController::class, "getStoreById"]);

// get All Stores 
Route::get("/getAllStores", [StoreController::class, "showAll"]);




Route::post('/send-email', function (\Illuminate\Http\Request $request) {

    $request->validate([
        'email' => 'required|email',
        'subject' => 'required|string',
        'message' => 'required|string',
    ]);

    $data = $request->only('email', 'subject', 'message');

    Mail::to($data['email'])->send(new SendMessageMail($data));

    return response()->json([
        'message' => 'تم إرسال الإيميل بنجاح'
    ]);
});



// Dashboard And Admin Route 

Route::middleware(["auth:sanctum", "admin"])->group(function(){

    // get the overview data for show 
Route::get('/Overview', [OverviewController::class, "index"]);

// add a new product 
Route::post('/add-product', [ProductDetailController::class, "create"]);

// edit a product 
Route::post('/edit-product/{id}', [ProductDetailController::class, "update"]);

// for git product page to page like first 20 product for one page and 40 products for 2 page 
Route::get('/allProducts', [ProductDetailController::class, "allProducts"]);

// this is for unit product you can get the unit we have in the database 
Route::get("/unit", [UnitController::class, "index"]);

// delete product with all think 
Route::delete("delete-product/{id}", [ProductDetail::class, 'destroy']);

// this is route to add a new car barnd 
Route::post("add-brand", [BrandsController::class, 'store']);

// this is route to Delete a car barnd 
Route::delete("delete-brand/{id}", [BrandsController::class, 'destroy']);

// this is route to edit a car barnd 
Route::post("update-brand/{id}", [BrandsController::class, 'update']);

// this is route to add a new car Moduel 
Route::post("add-moudle", [ModuleController::class, 'store']);

// this is route to Update a  car Moduel 
Route::post("update-model/{id}", [ModuleController::class, 'update']);

// this is route to Delete   car Moduel 
Route::delete("destroy-model/{id}", [ModuleController::class, 'destroy']);

// this is route to Delete   car Moduel 
Route::get("showAllEngines", [EngineeController::class, 'index']);

// add a new car engaine 
Route::post("add-engiene", [EngineeController::class, 'store']);

// Update a car engaine 
Route::post("update-engine/{id}", [EngineeController::class, 'update']);

// Update a car engaine 
Route::delete("delete-engine/{id}", [EngineeController::class, 'destroy']);

// add a new Catigoure 
Route::post("add-categorie", [CategoriesController::class, 'create']);

// Update a  Catigoure 
Route::post("edit-categorie/{id}", [CategoriesController::class, 'update']);

//delete a catigoure 
Route::delete('categorie/{id}', [CategoriesController::class, "destroy"]);

// get all Stores 
Route::get("allStores", [StoreController::class, "showAll"]);

// create a new store 
Route::post("createStore", [StoreController::class, "store"]);

// update a store 
Route::post("editStore/{id}", [StoreController::class, "update"]);

// Delete a store 
Route::delete("deleteStore/{id}", [StoreController::class, "destroy"]);

// get all orders 
Route::get('/orders', [OrderController::class, 'allOrders']);

// get all orders 
Route::get('/order/{id}', [OrderController::class, 'show']);

// create a new order 
Route::post("/createAnminOrder", [OrderController::class, "create"]);

// edit a order 
Route::post("/edit-order/{id}", [OrderController::class, "update"]);

// delete a order
Route::delete("/delete-order/{id}", [OrderController::class, "destroy"]);

// create admin acount
Route::post("/createAdminAcount", [AuthController::class, "createAdminAcount"]);

// gat all admin acount
Route::get("/getAllAdmins", [AuthController::class, "getAllAdmins"]);

// edit admin acount
Route::post("/editAdminAcount/{id}", [AuthController::class, "editAdminAccount"]);

// delete admin acount
Route::delete("/deleteAdmin/{id}", [AuthController::class, "deleteAdmin"]);

// create a new Article
Route::post("/createArticle", [ArticlesController::class, "store"]);

// edit a Article
Route::post("/editArticle/{id}", [ArticlesController::class, "update"]);

// delete a Article
Route::delete("/deleteArticle/{id}", [ArticlesController::class, "destroy"]);

// get all tikets
Route::get("/allTikets", [ConversationController::class, "index"]);

// get  tiket by id
Route::get("/conversation/{id}", [ConversationController::class, "show"]);

// support chat add message
Route::post("/support/addMessage", [MessageController::class, "supportAddMessage"]);

});