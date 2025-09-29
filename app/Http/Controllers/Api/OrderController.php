<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    // التحقق من تسجيل الدخول
    $user_id = auth()->id();
    if (!$user_id) {
        return response()->json([
            "message" => "يجب تسجيل الدخول قبل اتمام الشراء"
        ], 401);
    }

    // التحقق من الحقول المطلوبة
    $requiredFields = ['city', 'country', 'email', 'name', 'phone', 'streetAddress', 'cart'];
    $missingFields = [];
    foreach ($requiredFields as $field) {
        if (!$request->filled($field)) {
            $missingFields[] = $field;
        }
    }
    if (!empty($missingFields)) {
        return response()->json([
            "message" => "الحقول التالية مطلوبة",
            "fields" => $missingFields
        ], 422);
    }

    $cart = $request->cart; // مصفوفة المنتجات
    $total_price = 0;
    $order_items = [];

    // التحقق من كل منتج وجلب السعر من قاعدة البيانات
    foreach ($cart as $item) {
        $product = ProductDetail::find($item["product_id"]);
        if (!$product) {
            return response()->json([
                "message" => "المنتج غير موجود: ".$item['product_id']
            ], 404);
        }

        // حساب السعر الإجمالي للمنتج
        $product_total = $product->price * $item["quantity"];
        $total_price += $product_total;

        $order_items[] = [
            "product_id" => $product->id,
            "quantity" => $item["quantity"],
            "price" => $product->price,
            "total" => $product_total
        ];
    }

    // حفظ الطلب
    $order = Order::create([
        "user_id" => $user_id,
        "name" => $request->name,
        "country" => $request->country,
        "email" => $request->email,
        "city" => $request->city,
        "orderNotes" => $request->orderNotes, // اختياري
        "phone" => $request->phone,
        "streetAddress" => $request->streetAddress,
        "totlePrice" => $total_price
    ]);

    // ربط كل منتج بالطلب
    foreach ($order_items as $item) {
        OrderItem::create([
            "order_id" => $order->id,
            "product_id" => $item['product_id'],
            "quantity" => $item['quantity'],
            "price" => $item['price'],
            "total" => $item['total']
        ]);
    }

    return response()->json([
        "message" => "تم حفظ الطلب بنجاح",
        "order_id" => $order->id
    ], 200);
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
    public function store(StoreOrderRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
