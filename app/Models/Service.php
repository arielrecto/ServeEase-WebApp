<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'thumbnail',
        'price_type',
        'description',
        'terms_and_conditions',
        'is_approved',
        'service_type',
        'barangay_id',
        'user_id'
    ];

    protected $appends = ['avg_rate'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availService()
    {
        return $this->hasMany(AvailService::class);
    }

    protected function getGroupedRatings()
    {
        $bookings = $this->availService;
        $groupedTotalRatings = [];

        for ($i = 1; $i <= 5; $i++) {
            $groupedTotalRatings[$i] = $bookings->where('rate', $i)->count();
        }

        return $groupedTotalRatings;
    }

    // ACCESSORS
    public function getAvgRateAttribute()
    {
        $groupedTotalRatings = $this->getGroupedRatings();

        $totalRatings = array_sum($groupedTotalRatings);

        $ratingSum = array_sum(array_map(fn($key, $value) => $key * $value, array_keys($groupedTotalRatings), $groupedTotalRatings));

        // if there are no ratings, return 0
        if ($totalRatings === 0) {
            return 0;
        }

        $avgRate = $ratingSum / $totalRatings;

        // Return the formatted average rating
        return number_format($avgRate, 1);

    }
}
