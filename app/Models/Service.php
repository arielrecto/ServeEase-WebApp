<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    protected $appends = ['avg_rate', 'is_added_to_favorites'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availService()
    {
        return $this->hasMany(AvailService::class);
    }

    /**
     * Get the barangay that owns the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    /**
     * The users that belong to the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'service_users');
    }

    protected function getGroupedRatings()
    {
        $feedback = collect($this->availService)->map(function ($booking) {
            return $booking->feedback;
        });

        $groupedTotalRatings = [];

        for ($i = 1; $i <= 5; $i++) {
            array_push($groupedTotalRatings, $feedback->where('rate', $i)->count());
        }

        return $groupedTotalRatings;
    }

    // ACCESSORS
    public function getAvgRateAttribute()
    {
        $groupedTotalRatings = $this->getGroupedRatings();

        $totalRatings = array_sum($groupedTotalRatings);

        // if there are no ratings, return 0
        if ($totalRatings === 0) {
            return 0;
        }

        $avgRate = number_format($totalRatings / 15, 1);

        // Return the formatted average rating
        return "{$avgRate}";
    }

    public function getIsAddedToFavoritesAttribute()
    {
        if (!auth()->user()) {
            return false;
        }

        return auth()->user()
            ->favorites()
            ->where('service_id', $this->id)
            ->exists();
    }
}
