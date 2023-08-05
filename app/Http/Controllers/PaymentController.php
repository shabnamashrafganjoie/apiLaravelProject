<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function send(){





        

        if (isset($_POST['amount'])) {
            $amount = $_POST['amount'];
        } else {
            $amount = 1000;
        }
        if (isset($_POST['clientRefId'])) {
            $clientRefId = $_POST['clientRefId'];
        } else {
            $clientRefId = "shabnam.ashraf.ganjoie@gmail.com";
        }
        if (isset($_POST['Description'])) {
            $desc = $_POST['Description'];
        } else {
            $desc = 'پرداخت تستی ';
        }
        $payerIdentity = time();
        
        //توکن شما
        $tokenCode = "O0F8s_4O7cMyTrs4-cqpd3qDWxsgXGndzwfKtLGXHBA";
        //$callback = "https://digikala.com";

        $returnUrl = "http://localhost:8000/payment/verify";
        
        $data = array(
            'clientRefId' => $clientRefId,
            'payerIdentity' => $payerIdentity,
            'Amount' => $amount,
            'Description' => $desc,
            'returnUrl' => $returnUrl
        );


        
        try {
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
                    "authorization: Bearer " . $tokenCode,
                    "cache-control: no-cache",
                    "content-type: application/json",  
                ),
                    )
            );

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
        //شروع مرحله دو
                        $newURL = 'https://api.payping.ir/v1/pay/gotoipg/' . $response;

                        header('Location: ' . $newURL);
                        echo $newURL;
                    } else {
                        dd('else1');
                        $msg = ' تراکنش ناموفق بود- شرح خطا : عدم وجود کد ارجاع ';
                    }
                } elseif ($header['http_code'] == 400) {
                    dd('else2');

                    $msg = ' تراکنش ناموفق بود- شرح خطا : ' . $response;
                } else {
                    dd('else3');

                    $msg = ' تراکنش ناموفق بود- شرح خطا :' . $header['http_code'];
                }
            }
        } catch (Exception $e) {
            dd('ex');

            $msg = ' تراکنش ناموفق بود- شرح خطا سمت برنامه شما : ' . $e->getMessage();
        }




    }







    public function verify() {


        dd('hi verify');
    }
}
