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
        'total_hours',
        'start_date',
        'end_date',
        'remarks',
        'total_price',
        'service_cart_id',
    ];

    protected $appends = ['has_feedback'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function feedback()
    {
        return $this->hasOne(FeedBack::class);
    }


    public function rescheduleService()
    {
        return $this->hasMany(RescheduleService::class);
    }

    public function getHasFeedbackAttribute()
    {
        return $this->feedback()->exists();
    }


    public function serviceCart()
    {
        return $this->belongsTo(ServiceCart::class);
    }
}
