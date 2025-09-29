<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class ProductDetail extends Model
{
    use HasFactory;

    protected $table = 'product_details'; // تأكد من هذا الاسم

    public function product (){
        return $this->hasMany(ProductCompatibility::class);
    }
    public function favorites (){
        return $this->hasMany(Favorite::class);
    }
    public function Categories (){
        return $this->belongsTo(Categories::class, "categories_id");
    }
    public function productUnits(){
        return $this->hasMany(product_units::class, "product_id");
    }
    // public function reviees(){
    //     return $this->hasMany(Review::class, "product_id");
    // }
    public function reviews(){
        return $this->hasMany(Review::class, "product_id");
    }
    public function cart(){
        return $this->hasMany(Cart::class);
    }
    public function part_number(){
        return $this->hasMany(Alternative_parts::class, "product_id");
    }
    public function images(){
        return $this->hasMany(Product_images::class, "product_id");
    }

}
