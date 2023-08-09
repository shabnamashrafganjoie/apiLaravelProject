<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




class OrderController extends Controller
{



    public static function createOrder($data,$request,$amounts){

        DB::beginTransaction();


        $order = Order::create([

            'user_id' => $request->user_id ,
            'total_amount' => $amounts['totalAmount'] ,
            'delivery_amount' => $amounts['deliveryAmount'],
            'paying_amount' => $amounts['payingAmount'],
            'payment_status' => 0,	
            'payment_msg'=>''


        ]);

        foreach($request->order_items as $order_item){


            $product=Product::FindorFail($order_item['product_id']);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product -> id,
                'price'	=> $product -> price,
                'quantity' => $order_item['quantity'],
                'subtotal' => $product->price * $order_item['quantity']

            ]);

        }



        Transaction::create([

            'client_refid' => $data['clientRefId'],
            'user_id' => $request->user_id,
            'order_id' => $order->id,

            'amount' =>$amounts['payingAmount'],
            'request_from' => $request->request_from,
        ]);

        DB::Commit();


    }

    public static function updateOrder($response,$clientRefId){

        DB::beginTransaction();

$fin=Transaction::where('client_refid',$clientRefId)->first();


        $fin->update([
            'token'	 => $response,
         ]);

        DB::Commit();
    }








    // public static function create($request,$amounts,$response){




    //     DB::beginTransaction();
    //             $order = Order::create([
        
    //                 'user_id' => $request->user_id ,
    //                 'total_amount' => $amounts['totalAmount'] ,
    //                 'delivery_amount' => $amounts['deliveryAmount'],
    //                 'paying_amount' => $amounts['payingAmount'],
    //                 'payment_status' => 0,	
        
    //             ]);
        
        
    //             foreach($request->order_items as $order_item){
        
        
    //                 $product=Product::FindorFail($order_item['product_id']);
        
    //                 OrderItem::create([
    //                     'order_id' => $order->id,
    //                     'product_id' => $product -> id,
    //                     'price'	=> $product -> price,
    //                     'quantity' => $order_item['quantity'],
    //                     'subtotal' => $product->price * $order_item['quantity']
        
    //                 ]);
        
    //             }
        
        
    //             Transaction::create([
        
    //                 'user_id' => $request->user_id,
    //                 'order_id' => $order->id,
    //                 'amount' =>$amounts['payingAmount'],
    //                 'code'	 => $response,
    //                 'request_from' => $request->request_from
        
    //             ]);
        
        
        
    //             DB::Commit();
    //         }
        
        






    public static function updateVerify($response,$msg,$header,$request){

        if($header == 200){

            DB::beginTransaction();
            $find_record=Transaction::where('client_refid',$request->clientRefId)->first();
            $order_id=$find_record->order_id;

            $find_record->update([

                'refid'=>$request->refId,
                'status'=>1,
                
            ]);

            $find_order=Order::where('id',$order_id)->first();

            $find_order->update([

                'status'=>1,
                'payment_status'=>$header,
                'payment_msg'=>$msg
                
            ]);

            DB::commit();
           

        }

        if($header == 400){
            DB::beginTransaction();
            $find_record=Transaction::where('client_refid',$request->clientRefId)->first();
            $order_id=$find_record->order_id;

            $find_record->update([

                'refid'=>$request->refId,
                'status'=>0,
                
            ]);

            $find_order=Order::where('id',$order_id)->first();

            $find_order->update([

                'status'=>0,
                'payment_status'=>$header,
                'payment_msg'=>$msg
                
            ]);

            DB::commit();

        }


    }



}



