<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\ProviderProfile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
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
        'is_suspended',
        'email_verified_at', // TODO: remove in production
    ];

    protected $appends = ['user_status'];

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

        return ProviderProfile::where('profile_id', $profile->id)->exists();
    }

    public function hasVerifiedProviderProfile()
    {
        $profile = $this->profile;

        if (!$profile) {
            return false;
        }

        return ProviderProfile::where('profile_id', $profile->id)->whereNotNull('verified_at')->exists();
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

    public function notifications()
    {
        return $this->hasMany(Notification::class);
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

    public function serviceCarts()
    {
        return $this->hasMany(ServiceCart::class);
    }


    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

    public function paymentAccounts()
    {
        return $this->hasMany(PaymentAccount::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'paid_by')
            ->orWhere('paid_to', $this->id);
    }

    public function userStatus(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->is_suspended ? 'Suspended' : 'Active',
        );
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function reportedBy()
    {
        return $this->hasMany(Report::class, 'reported_by');
    }
}
