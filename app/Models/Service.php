<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
        'service_type_id',
        'barangay_id',
        'user_id'
    ];

    protected $appends = ['avg_rate', 'is_added_to_favorites', 'service_thumbnail', 'total_review_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the feedbacks for the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function feedbacks(): HasManyThrough
    {
        return $this->hasManyThrough(FeedBack::class, AvailService::class, 'service_id', 'avail_service_id');
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
     * Get the serviceType that owns the Service
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
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
    public function avgRate(): Attribute
    {
        return Attribute::make(
            get: function () {
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
        );
    }

    public function totalReviewCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->availService()->whereHas('feedback')->count();
            }
        );
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

    public function getServiceThumbnailAttribute()
    {
        return $this->thumbnail ?? '/assets/images/default_thumbnail.png';
    }

    public function scopeOrderByAvgRate($query, $direction = 'desc')
    {
        return $query->get()->sortBy(function ($service) {
            return $service->avg_rate;
        }, SORT_REGULAR, $direction === 'desc')->values();
    }
}
