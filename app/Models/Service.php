<?php

namespace App\Models;

use Carbon\Carbon;
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
        'description',
        'price_type',
        'terms_and_conditions',
        'service_type_id',
        'is_approved',
        'barangay_id',
        'user_id',
        'is_quantifiable',
        'archived_at'
    ];

    protected $casts = [
        'archived_at' => 'datetime'
    ];

    protected $appends = ['avg_rate', 'is_added_to_favorites', 'service_thumbnail', 'total_review_count', 'weekly_revenue'];

    protected static function booted()
    {
        static::addGlobalScope('active', function ($query) {
            if (!request()->routeIs('service-provider.*')) {
                $query->whereNull('archived_at');
            }
        });
    }

    public function scopeWithArchived($query)
    {
        return $query->withoutGlobalScope('active');
    }

    public function scopeArchived($query)
    {
        return $query->withoutGlobalScope('active')->whereNotNull('archived_at');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

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
        return $this->hasManyThrough(FeedBack::class, AvailService::class, 'service_id', 'avail_service_id')
            ->when(!request()->routeIs('service-provider.*'), function ($query) {
                return $query->whereNull('archived_at');
            });
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

    public function canBeBooked()
    {
        return is_null($this->archived_at);
    }


    protected function getGroupedRatings(): array
    {
        // Only consider AvailServices that have feedback and valid numeric ratings
        $ratings = collect($this->availService)
            ->filter(
                fn($booking) =>
                $booking->relationLoaded('feedback') // Optional but efficient
                && $booking->feedback
                && is_numeric($booking->feedback->rate)
            )
            ->pluck('feedback.rate')
            ->map(fn($rate) => round($rate)) // Round decimals to nearest int
            ->filter(fn($rate) => in_array($rate, range(1, 5))); // Keep only 1–5

        // Return grouped rating counts from 1 to 5
        return collect(range(1, 5))->map(function ($rate) use ($ratings) {
            return $ratings->filter(fn($r) => $r === $rate)->count();
        })->toArray();
    }

    public function avgRate(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ratings = FeedBack::whereHas('availService', function ($q) {
                    $q->where('service_id', $this->id);
                })
                    ->pluck('rate')
                    ->filter(fn($rate) => is_numeric($rate) && in_array((int) $rate, range(1, 5)))
                    ->map(fn($rate) => round($rate));

                $totalRatings = $ratings->count();

                if ($totalRatings === 0) {
                    return '0.0';
                }

                $weightedSum = $ratings->sum();
                return number_format($weightedSum / $totalRatings, 1);
            }
        );
    }

    public function totalReviewCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bookings = $this->availService()->whereHas('feedback')->pluck('id')->toArray();

                $feedbacks = FeedBack::whereIn('avail_service_id', $bookings)->count();

                return $feedbacks;
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

    public function weeklyRevenue(): Attribute
    {
        return Attribute::make(
            get: function () {
                $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d H:i');
                $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d H:i');

                return $this->availService()
                    ->with('transactions')
                    ->whereHas('transactions', function ($query) use ($startOfWeek, $endOfWeek) {
                        $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                    })
                    ->withSum([
                        'transactions as weekly_revenue' => function ($query) use ($startOfWeek, $endOfWeek) {
                            $query->where('status', 'approved')
                                ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
                        }
                    ], 'amount')
                    ->value('weekly_revenue') ?? 0;
            }
        );
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
