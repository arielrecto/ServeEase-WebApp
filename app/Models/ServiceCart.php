<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'total_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availServices()
    {
        return $this->hasMany(AvailService::class);
    }
    public function getTotalAmountAttribute($value)
    {
        return number_format($value, 2);
    }
    public function setTotalAmountAttribute($value)
    {
        $this->attributes['total_amount'] = str_replace(',', '', $value);
    }
    public function getReferenceNumberAttribute($value)
    {
        return strtoupper($value);
    }

    public function remarks()
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }
}
