<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    public function products(){
        return $this->hasMany(ProductDetail::class, "categories_id");
    }
    public function ad(){
        return $this->hasMany(Advertisements::class, "categorie_id");
    }
}
