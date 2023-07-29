<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class city extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='cities';
    protected $guarded=[];
}
