<?php

namespace App\Models;


use App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category as ModelsCategory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='categories';
    protected $guarded=[];



    public function children(){

        return $this->hasMany('App\Models\Category','parent_id');
    }


    public function parent(){

        return $this->belongsTo(Category::class,'parent_id');
    }



    public function products(){

        return $this->hasMany(Product::class);
    }

}
