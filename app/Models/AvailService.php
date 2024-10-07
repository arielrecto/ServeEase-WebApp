<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailService extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'service_id',
        'status',
        'start_date',
        'end_date',
        'remark',
        'total_price'
    ];



    public function user(){
        return $this->belongsTo(User::class);
    }

    public function service(){
        return $this->belongsTo(Service::class);
    }

    public function feedback(){
        return $this->hasOne(FeedBack::class);
    }


    public function rescheduleService(){
        return $this->hasMany(RescheduleService::class);
    }
}
