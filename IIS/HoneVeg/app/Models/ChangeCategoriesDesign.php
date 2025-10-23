<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeCategoriesDesign extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'change_categories_designs';
    protected $primaryKey = 'design_id';
    public $timestamps = false;

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id', 'user_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_category_id', 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'user_id');
    }

    public function labels()
    {
        return $this->hasMany(DesignLabels::class, 'design_id', 'design_id');
    }
}
