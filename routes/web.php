<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;






Route::get('/payment/verify', function (Request $request) {
dd($request->all());
});
