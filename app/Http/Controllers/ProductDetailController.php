<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductDetailRequest;

use App\Models\Alternative_parts;
use App\Models\Brands;
use App\Models\Product_images;
use App\Models\product_units;
use App\Models\ProductCompatibility;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    // الخطوة 1: البحث عن البراند حسب الاسم أو الـ ID
    $brand_ids = Brands::where('id', $query)
        ->orWhere('ar_name', 'LIKE', "%{$query}%")
        ->orWhere('en_name', 'LIKE', "%{$query}%")
        ->pluck('id'); // نأخذ فقط الـ IDs

    // الخطوة 2: جلب كل المنتجات التابعة لتلك البراندات من جدول ProductCompatibility
    $product_ids = ProductCompatibility::whereIn('brand_id', $brand_ids)
        ->pluck('product_detail_id');

    // الخطوة 3: جلب تفاصيل المنتجات
    $products = ProductDetail::whereIn('id', $product_ids)
        ->with('reviews')
        ->withCount('reviews')
        ->get();

    return response()->json($products);
}


    public function index($id)
    {
        return ProductDetail::find($id);
        
    }

    public function allProducts (){
        $Products = ProductDetail::with("Categories")
        ->with("productUnits")
        ->paginate(20);

        return $Products;
    }

    /**
     * Show the form for creating a new resource.
     */

public function create(Request $request)
{
    
    $validator = Validator::make($request->all(), [
        "arName"        => "required|string|max:255",
        "enName"        => "required|string|max:255",
        "arDescription" => "required|string",
        "enDescription" => "required|string",
        "price"         => "required|numeric|min:1",
        "old_price"     => "nullable|numeric",
        "categories_id" => "required|exists:categories,id",
        "Images"        => "required",
        "Images.*"      => "image|mimes:jpg,jpeg,png|max:2048",
    ], [
        "arName.required"        => "الاسم بالعربية مطلوب.",
        "enName.required"        => "الاسم بالإنجليزية مطلوب.",
        "arDescription.required" => "الوصف بالعربية مطلوب.",
        "enDescription.required" => "الوصف بالإنجليزية مطلوب.",
        "price.required"         => "يجب إدخال السعر.",
        "price.numeric"          => "السعر يجب أن يكون رقمًا.",
        "price.min"              => "السعر يجب أن يكون أكبر من صفر.",
        "old_price.numeric"      => "السعر القديم يجب أن يكون رقمًا.",
        "categories_id.required" => "يجب اختيار القسم.",
        "categories_id.exists"   => "القسم غير موجود.",
        "Images.required"        => "يجب رفع صورة واحدة على الأقل.",
        "Images.*.image"         => "كل ملف يجب أن يكون صورة.",
        "Images.*.mimes"         => "الصورة يجب أن تكون بصيغة jpg أو jpeg أو png.",
        "Images.*.max"           => "حجم الصورة يجب ألا يتجاوز 2MB.",
    ]);

    if ($validator->fails()) {
    return response()->json([
        "error" => $validator->errors()->first()
    ], 422);
}


    
    $product = ProductDetail::create([
        "ar_name"        => $request->arName,
        "en_name"        => $request->enName,
        "ar_description" => $request->arDescription,
        "en_description" => $request->enDescription,
        "price"          => $request->price,
        "old_price"      => $request->old_price,
        "thumbnail"      => "",
        "categories_id"  => $request->categories_id,
        "store_id"       => 1,
    ]);

    ProductCompatibility::create([
        "product_detail_id" => $product->id,
        "brand_id"          => $request->brand_id,
        "model_id"          => $request->model_id,
        "model_date_id"     => $request->model_date_id,
        "engine_id"         => $request->engine_id,
    ]);

    if ($request->hasFile("Images")) {
        $first = true;
        foreach ($request->file("Images") as $image) {
            $path = $image->store("products_images", "public");
           $fullUrl = asset("storage/" . $path);
            if ($first) {
                $product->thumbnail = $fullUrl;
                $product->save();
                $first = false;
            }

            Product_images::create([
                "product_id" => $product->id,
                "image_url"  => $fullUrl,
            ]);
        }
    }

    if ($request->alternative_parts) {
        $pairs = explode("|", $request->alternative_parts);
        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if (!$pair) continue;

            [$company, $numbers] = array_pad(explode(":", $pair, 2), 2, null);

            if ($company && $numbers) {
                foreach (explode(",", $numbers) as $num) {
                    Alternative_parts::create([
                        "product_id"   => $product->id,
                        "company"      => trim($company),
                        "part_number"  => trim($num)
                    ]);
                }
            }
        }
    }

    product_units::create([
        "product_id" => $product->id,
        "unit_id" => $request->unit_id,
        "stock" => $request->stock,
        "price" => $product->price
    ]);

    return response()->json(["message" => "تم إنشاء المنتج بنجاح."]);
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
public function update(Request $request, $id)
{
    try {
        
        $validator = Validator::make($request->all(), [
            "arName"        => "required|string|max:255",
            "enName"        => "required|string|max:255",
            "arDescription" => "required|string",
            "enDescription" => "required|string",
            "price"         => "required|numeric|min:1",
            "old_price"     => "nullable|numeric",
            "categories_id" => "required|exists:categories,id",
            "Images.*"      => "image|mimes:jpg,jpeg,png|max:2048",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()->first()
            ], 422);
        }

        
        $product = ProductDetail::findOrFail($id);

        
        $product->update([
            "ar_name"        => $request->arName,
            "en_name"        => $request->enName,
            "ar_description" => $request->arDescription,
            "en_description" => $request->enDescription,
            "price"          => $request->price,
            "old_price"      => $request->old_price ?? 0,
            "categories_id"  => $request->categories_id,
        ]);

    
        if ($request->has('deletedImages') && is_array($request->deletedImages)) {
    foreach ($request->deletedImages as $imgId) {
        $img = Product_images::find($imgId);
        if ($img) {
            try {
                
                if (Storage::disk('public')->exists($img->image_url)) {
                    Storage::disk('public')->delete($img->image_url);
                }


                $img->forceDelete(); 
            } catch (\Exception $e) {
     
                return response()->json("Failed to delete image ID {$imgId}: ");
            }
        }
    }
}


        // إضافة صور جديدة
        if ($request->hasFile("Images")) {
            foreach ($request->file("Images") as $image) {
                $path = $image->store("products_images", "public");

                $fullUrl = asset("storage/" . $path);

                Product_images::create([
                    "product_id" => $product->id,
                    "image_url"  => $fullUrl,
                ]);
            }
        }


        $remainingImages = Product_images::where("product_id", $product->id)->pluck("image_url")->toArray();
        if (empty($remainingImages)) {
            $product->thumbnail = null;
        } elseif (!in_array($product->thumbnail, $remainingImages)) {
            $product->thumbnail = $remainingImages[0];
        }
        $product->save();

        // تحديث القطع البديلة
        Alternative_parts::where("product_id", $product->id)->delete();
        if ($request->alternative_parts) {
            $pairs = explode("|", $request->alternative_parts);
            foreach ($pairs as $pair) {
                $pair = trim($pair);
                if (!$pair) continue;

                [$company, $numbers] = array_pad(explode(":", $pair, 2), 2, null);

                if ($company && $numbers) {
                    foreach (explode(",", $numbers) as $num) {
                        Alternative_parts::create([
                            "product_id"   => $product->id,
                            "company"      => trim($company),
                            "part_number"  => trim($num)
                        ]);
                    }
                }
            }
        }

        // تحديث الوحدات
        product_units::where("product_id", $product->id)->delete();
        if ($request->unit_id && $request->stock) {
            product_units::create([
                "product_id" => $product->id,
                "unit_id"    => $request->unit_id,
                "stock"      => $request->stock,
                "price"      => $product->price
            ]);
        }

        return response()->json(["message" => "تم تعديل المنتج بنجاح."]);

    } catch (\Exception $e) {
        return response()->json([
            "error" => "حدث خطأ أثناء تعديل المنتج: " . $e->getMessage()
        ], 500);
    }
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $product = ProductDetail::with('images')
    ->with("product")
    ->with("favorites")
    ->with("productUnits")
    ->with("reviews")
    ->with("cart")
    ->with("part_number")
    ->find($id);

    if (!$product) {
        return response()->json(['error' => 'المنتج غير موجود'], 404);
    }

    try {
        // 1- حذف الصور من التخزين وقاعدة البيانات
        foreach ($product->images as $img) {
            if (Storage::disk('public')->exists($img->image_url)) {
                Storage::disk('public')->delete($img->image_url);
            }
            $img->forceDelete();
        }

        // 2- حذف المنتج نفسه
        $product->forceDelete();

        return response()->json(['message' => 'تم حذف المنتج وكل ما يرتبط به بنجاح'], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'فشل الحذف: ' . $e->getMessage()
        ], 500);
    }
}
}
