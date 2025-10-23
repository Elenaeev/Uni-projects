<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignLabels extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'design_labels';
    protected $primaryKey = 'design_label_id';
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(ChangeCategoriesDesign::class, 'design_id', 'design_id');
    }
}
