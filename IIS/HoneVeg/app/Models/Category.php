<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    public function subcategoryDesigns()
    {
        return $this->hasMany(ChangeCategoriesDesign::class, 'parent_category_id', 'category_id');
    }

    public function labels()
    {
        return $this->hasMany(Label::class, 'category_id', 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category_id', 'category_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_category_id', 'category_id');
    }
}
