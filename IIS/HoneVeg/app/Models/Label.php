<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'labels';
    protected $primaryKey = 'label_id';
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
    
}
