<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProviderProfile extends Model
{
    use HasFactory;



    protected $fillable = [
        'service_type_id',
        'experience',
        'contact',
        'verified_at',
        'certificate',
        'profile_id'
    ];


    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the serviceType that owns the ProviderProfile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
