<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='products';
    protected $guarded=[];


    public function images(){
        return $this->hasMany(ProductImage::class,'product_id');
    }



    public function brands(){

        return $this->belongsTo(Brand::class,'product_id');
    }

}
