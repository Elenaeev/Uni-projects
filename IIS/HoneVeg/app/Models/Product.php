<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $timestamps = false;
        
    public function labels()
    {
        return $this->hasManyThrough(Label::class, ProductLabelValue::class, 'product_id', 'label_id', 'product_id', 'label_id');
    }

    public function labelValues()
    {
        return $this->hasMany(ProductLabelValue::class, 'product_id', 'product_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_user_id', 'user_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_products', 'product_id', 'event_id', 'product_id', 'event_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Product::class, 'order_product_lists', 'product_id', 'order_id', 'product_id', 'order_id');
    }

    public function ratedBy()
    {   
        return $this->belongsToMany(User::class, 'ratings', 'product_id', 'user_id', 'product_id', 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'product_id', 'product_id');
    }
     
}

