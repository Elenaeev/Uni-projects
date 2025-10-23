<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipants extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'event_participants';
    protected $primaryKey = 'event_participants_id';
    public $timestamps = false;
}
