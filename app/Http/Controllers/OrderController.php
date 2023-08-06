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
    public static function create($request,$amounts,$response){




DB::beginTransaction();
        $order = Order::create([

            'user_id' => $request->user_id ,
            'total_amount' => $amounts['totalAmount'] ,
            'delivery_amount' => $amounts['deliveryAmount'],
            'paying_amount' => $amounts['payingAmount'],
            'payment_status' => 0,	

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

            'user_id' => $request->user_id,
            'order_id' => $order->id,
            'amount' =>$amounts['payingAmount'],
            'token'	 => $response,
            'request_from' => $request->request_from

        ]);



        DB::Commit();
    }
}



