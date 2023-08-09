<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Response;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class PaymentController extends ApiController
{
    public function send(Request $request){
      
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'order_items' => 'required',
            'order_items.*.product_id' => 'required|integer',
            'order_items.*.quantity' => 'required|integer',
            'request_from' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        $totalAmount = 0;
        $deliveryAmount = 0;

        foreach ($request->order_items as $orderItem) {
            $product = Product::findOrFail($orderItem['product_id']);
            if ($product->quantity < $orderItem['quantity']) {
                return $this->errorResponse('The product quantity is incorrect', 422);
            }
        
            $totalAmount += $product->price * $orderItem['quantity'];
            $deliveryAmount += $product->delivery_amount;
        
        }


        $payingAmount = $totalAmount + $deliveryAmount;

        $amounts=[

            'totalAmount' => $totalAmount,
            'deliveryAmount' => $deliveryAmount,
            'payingAmount' => $payingAmount,


        ];





        
        $amount=$payingAmount;

        $clientRefId=time().rand(111111,999999);
        $desc = 'پرداخت تستی ';

    
      
        $payerIdentity = time();
        
        //توکن شما
        $TokenCode = "O0F8s_4O7cMyTrs4-cqpd3qDWxsgXGndzwfKtLGXHBA";

        $returnUrl = "http://localhost/apiLaravelProject/public/payment/verify";



        $data = array(
            'clientRefId'   => $clientRefId,
            'payerIdentity' => $payerIdentity,
            'Amount'        => $amount,
            'Description'   => $desc,
            'returnUrl'     => $returnUrl
        );
        
        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.payping.ir/v1/pay",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 45,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    "authorization: Bearer " . $TokenCode,
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
                    )
            );

                 $createOrder= OrderController::createOrder($data,$request,$amounts);

        // $data = array(
        //     'clientRefId' => $clientRefId,
        //     'payerIdentity' => $payerIdentity,
        //     'amount' => $payingAmount,
        //     'description' => $desc,
        //     'returnUrl' => $returnUrl
        // );

        // $createOrder= OrderController::createOrder($data,$request,$amounts);

        
        // try {
        //     $curl = curl_init();
        //     curl_setopt_array($curl, array(
        //         CURLOPT_URL => "https://api.payping.ir/v1/pay",
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => "",
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 45,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => "POST",
        //         CURLOPT_POSTFIELDS => json_encode($data),
        //         CURLOPT_HTTPHEADER => array(
        //             "accept: application/json",
        //             "authorization: Bearer " . $tokenCode,
        //             "cache-control: no-cache",
        //             "content-type: application/json",  
        //         ),
        //             )
        //     );


            $response = curl_exec( $curl );

            $header = curl_getinfo($curl);


            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $msg = 'کد خطا: CURL#' . $err;
                $erro = 'در اتصال به درگاه مشکلی پیش آمد.';
                return false;
            } else {
                if ($header['http_code'] == 200) {
                    $response = json_decode( $response, true );
                    if (isset($response) and $response != '') {
                        $response = $response['code'];

                   //صدازدن متد کرییت برای اضافه کردن در جدول
                  $updateorder= OrderController::updateOrder($response,$clientRefId);


                        //شروع مرحله دو
                        $newURL = 'https://api.payping.ir/v1/pay/gotoipg/' . $response;


                        header('Location: ' . $newURL);
                        echo $newURL;
                    } else {
                        $msg = ' تراکنش ناموفق بود- شرح خطا : عدم وجود کد ارجاع ';
                    }
                } elseif ($header['http_code'] == 400) {

                    $msg = ' تراکنش ناموفق بود- شرح خطا : ' . $response;
                } else {

                    $msg = ' تراکنش ناموفق بود- شرح خطا :' . $header['http_code'];
                }
            }
        } catch (Exception $e) {

            $msg = ' تراکنش ناموفق بود- شرح خطا سمت برنامه شما : ' . $e->getMessage();
        }




    }





    public function verify(Request $request){

       $refId=$request->refId; 
       $clientRefId=$request->clientRefId; 

       $amount=Transaction::where('client_refid',$clientRefId)->first()->amount;
      


//توکن شما
$tokenCode = "O0F8s_4O7cMyTrs4-cqpd3qDWxsgXGndzwfKtLGXHBA";
$data = array(
    'amount' => $amount,
    'refId' => $refId
);
try {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.payping.ir/v1/pay/verify",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 45,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Bearer " . $tokenCode,
            "cache-control: no-cache",
            "content-type: application/json",
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);

    $header = curl_getinfo($curl);

    curl_close($curl);


    if ($err) {
        $msg = 'خطا در ارتباط به پی‌پینگ : شرح خطا ' . $err;
    } else {
        if ($header['http_code'] == 200) {
            $response = json_decode($response, true);
            if (isset($refId) and $refId != '') {
                 $msg = ' تراکنش موفق بود : ' . $refId;
                 $updateorder= OrderController::updateVerify($response,$msg,$header['http_code'],$request);

                $outp['msg'] = $msg;
            } else {
                $msg = 'متافسانه سامانه قادر به دریافت کد پیگیری نمی باشد! نتیجه درخواست : ' . $header['http_code'];
            }
        } elseif ($header['http_code'] == 400) {
            $msg = ' تراکنش ناموفق بود- شرح خطا : ' . $response;
            $updateorder= OrderController::updateVerify($response,$msg,$header['http_code'],$request);

            $outp['msg'] = $msg;
        } else {
            $msg = ' تراکنش ناموفق بود- شرح خطا : ' . $header['http_code'];
        }
    }
} catch (Exception $e) {
    $msg = ' تراکنش ناموفق بود- شرح خطا سمت برنامه شما : ' . $e->getMessage();
}
return $msg;
    }






}