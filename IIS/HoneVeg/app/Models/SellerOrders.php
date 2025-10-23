<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerOrders extends Model
{
    use HasFactory;

    protected $table = 'seller_orders';
    protected $primaryKey = 'seller_order_id';
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'seller_id');
    }

    public function orderProducts()
    {   
        return $this->belongsToMany(Product::class, "order_product_lists", "order_id", "product_id", "order_id", "product_id")->withPivot("product_amount");
    }
}
