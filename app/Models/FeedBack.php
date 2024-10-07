<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedBack extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'content',
        'rate',
        'avail_service_id',
    ];


    public function availService(){
        return $this->belongsTo(AvailService::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
