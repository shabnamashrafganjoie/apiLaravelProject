<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;




Route::apiResource('brands',BrandController::class);
Route::get('/brands/{brand}/products',[BrandController::class,'product']);


Route::apiResource('categories',CategoryController::class);
Route::get('/categories/{category}/products',[CategoryController::class,'product']);

Route::get('/categories/{category}/children',[CategoryController::class,'children']);
Route::get('/categories/{category}/parent',[CategoryController::class,'parent']);


Route::apiResource('products',ProductController::class);


Route::post('/payment/send',[PaymentController::class,'send']);





