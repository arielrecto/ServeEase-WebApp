<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RescheduleService extends Model
{
    use HasFactory;

    protected $fillable = [
        'avail_service_id',
        'start_date',
        'end_date',
        'user_id',
        'status',
        'remark'
    ];


    public function availService(){
        return $this->belongsTo(availService::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
