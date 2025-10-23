<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'events';
    protected $primaryKey = 'event_id';
    public $timestamps = false;

    public function seller()
    {
        return $this->belongsTo(User::class, $foreignKey='seller_id', $ownerKey='user_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants', 'event_id', 'user_id', 'event_id', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'event_products', 'event_id', 'product_id', 'event_id', 'product_id');
    }


}
