<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    protected $appends = ['has_feedback', 'is_fully_paid'];

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

    protected function isFullyPaid(): Attribute
    {
        return Attribute::make(
            get: function () {
                $amountPaid = Transaction::where('transactionable_type', AvailService::class)
                    ->where('transactionable_id', $this->id)
                    ->where('status', 'approved')
                    ->get()
                    ->sum();

                return $amountPaid >= $this->total_price;
            }
        );
    }


    public function serviceCart()
    {
        return $this->belongsTo(ServiceCart::class);
    }

    public function availServiceRemarks()
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }
}
