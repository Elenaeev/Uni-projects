<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    protected $primaryKey = 'role_id';
    protected $table = 'roles';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    
    
}
