<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::get('/payment/verify',[PaymentController::class,'verify']);



Route::get('/payment/verify', function (Request $request) {

    $response=Http::post('http://localhost:8000/api/payment/verify', [ 
        
        'clientrefid' => $request->clientrefid,
       
        'refid' => $request->refid
        
]);

return $response;
    


    // $response = Http::post('http://localhost:8000/api/payment/verify' , [
    //     'clientrefid' => $request->clientrefid,
    //     'refid' => $request->refid
    // ]);

    //dd($response);
});






    
