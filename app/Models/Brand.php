<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='Brands';
    protected $guarded=[];


    public function products(){

        return $this->hasMany(Product::class);
    }
}
