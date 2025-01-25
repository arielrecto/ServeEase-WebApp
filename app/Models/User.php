<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\ProviderProfile;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Utilities
    public function hasProviderProfile()
    {
        $profile = $this->profile;

        if (!$profile) {
            return false;
        }

        return ProviderProfile::whereProfileId($profile->id)->exists();
    }

    public function hasVerifiedProviderProfile()
    {
        $profile = $this->profile;

        if (!$profile) {
            return false;
        }

        return Profile::whereId($profile->id)->whereNotNull('verified_at')->exists();
    }

    // Relations
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function availServices()
    {
        return $this->hasMany(AvailService::class);
    }


    public function feedbacks()
    {
        return $this->hasMany(FeedBack::class);
    }

    public function rescheduleServices()
    {
        return $this->hasMany(RescheduleService::class);
    }
    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'owner_id')
            ->orWhere('participant_id', $this->id);
    }
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id')
            ->orWhere('receiver_id');
    }

    /**
     * The favorites that belong to the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_users');
    }
}
