<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProductList extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'order_product_lists';
    protected $primaryKey = 'order_product_list_id';
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}

