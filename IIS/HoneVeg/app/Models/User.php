<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    protected $primaryKey = 'user_id';
    protected $guarded = [];
    public $timestamps = false;

    protected $table = 'users';

    public function saleProducts()
    {
        return $this->hasMany(Product::class, 'seller_user_id', 'user_id');
    }

    public function createdOrders()
    {
        return $this->hasMany(Order::class, 'customer_user_id', 'user_id');
    }

    public function roles()
    {
        return $this->hasMany(Roles::class, 'user_id', 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'user_id', 'user_id');
    }

    public function ratedProducts()
    {
        return $this->belongsToMany(Product::class, 'ratings', 'user_id', 'product_id', 'user_id', 'product_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_participants', 'user_id', 'event_id', 'user_id', 'events.event_id');
    }

    public function sellerOrders()
    {
        return $this->belongsToMany(Order::class, 'seller_orders', 'seller_id', 'order_id', 'user_id', 'order_id');
    }

    public function moderatedCategoryDesigns()
    {
        return $this->hasMany(ChangeCategoriesDesign::class, 'moderator_id', 'user_id');
    }

    public function createdCategoryDesigns()
    {
        return $this->hasMany(ChangeCategoriesDesign::class, 'creator_id', 'user_id');
    }
}
