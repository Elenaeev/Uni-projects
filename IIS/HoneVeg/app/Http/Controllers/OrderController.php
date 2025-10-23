<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProductList;
use App\Models\SellerOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    static public function refreshOrderStatus($order_id)
    {
        $order = Order::where('order_id', $order_id)->first();
        if(!$order)
            abort(500);

        $sellerOrders = DB::table("orders")
        ->join("seller_orders", "orders.order_id", "=", "seller_orders.order_id")
        ->where("seller_orders.order_id", $order_id);
        if(!$sellerOrders)
            abort(500);

        $statuses = $sellerOrders->select("status")->select("seller_orders.status as sell_status")->pluck('sell_status')->toArray();
        
        if($order->status != "cart")
        {
            $uniq = array_unique($statuses);
            if(count($uniq) === 1 && $uniq[0] == "canceled")//canceled - all sellers orders canceled
                $status = "canceled";
            elseif(in_array("accepted", $uniq) || count($uniq) == 0)
                $status = "in process";
            else //delivered - all non canceled are delivered (there is no accepted)
                $status = "delivered";
    
            $order->status = $status;
            switch ($status) {
                case "canceled":
                    $order->close_date = Carbon::now()->toDateTimeString(); 
                    break;
                case "delivered":
                    $order->delivery_date = Carbon::now()->toDateTimeString();
                    break;
                default:
                    break;
            }
            $order->save();
        }
    }


    public function index($order_id)
    {
        if(Gate::denies("be-order-creator", $order_id))
            abort(404);

        $order = Order::where('order_id', $order_id)->first();
        if(!$order)
            abort(404);
        
        $productList = DB::table("orders")
        ->join("order_product_lists", "orders.order_id", "=", "order_product_lists.order_id")
        ->join("products", "products.product_id", "=", "order_product_lists.product_id")
        ->where("order_product_lists.order_id", $order_id)->get();
        if(!$productList)
            abort(500);


        //define status    
        $this->refreshOrderStatus($order_id);
       

 

        return view("orderPage", [
            "orderProducts" => $productList,
            "order" => $order
        ]);
    }

    //order already exists as cart, we create seller order and change status
    public function create(Request $request, $order_id)
    {
        if(Gate::denies("be-order-creator", $order_id))
            abort(404);

        $order = Order::where("order_id", $order_id)->first();
        if(!$order)
            abort(500);

        $products = $order->products()->get();
        if(!$products)
            abort(500);

        DB::transaction(function () use ($order, $products) {
            foreach($products as $product)
            {
                $selOrd = new SellerOrders();
                $selOrd->order_id = $order->order_id;
                $selOrd->seller_id = $product->seller_user_id;
                $selOrd->status = "accepted";
                $selOrd->save();
            }
            $order->status = "in process";
            $order->creation_date = Carbon::now()->toDateTimeString();
            $order->save();
        });


        return redirect()->route("order.index", ["order_id" => $order_id]);
    }

    public function edit(Request $request, $order_id)
    {
        if(Gate::denies("be-order-participant", $order_id))
            abort(500);
    
        $orders = SellerOrders::where("order_id", $order_id)->get();
        if(!$orders)
            abort(500);

        foreach($orders as $ord)
        {
            switch ($request->input("whatToDo")) {
                case "accept":
                    $ord->status = "accepted";
                    break;
                case "cancel":
                    $ord->status = "canceled";
                    break;
                
                default:
                    break;
            }
            $ord->save();
        }

        return redirect()->route("profile.index", ["user_id"=>Auth::user()->user_id]);
    }

    public function delete(Request $request, $order_id)
    {
        if(Gate::denies("be-order-creator", $order_id))
            abort(500);
        
        $order = Order::where("order_id", $order_id)->first();
        if(!$order)
            abort(500);
        if($order->status != "cart")
            abort(404);

        $order->delete();

        $plist = OrderProductList::where("order_id", $order_id); 
        $plist->delete();


        return redirect()->route("profile.index", ["user_id"=>Auth::user()->user_id]);
    }
}
