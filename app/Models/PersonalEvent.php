<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalEvent extends Model
{
    use HasFactory;


    protected $fillable = [
        'event_name',
        'event_type',
        'start_date',
        'end_date',
        'description',
        'user_id',
        'is_public',
    ];


    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
}
