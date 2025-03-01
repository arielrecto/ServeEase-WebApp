<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;



    protected $fillable = [
        'avatar',
        'last_name',
        'first_name',
        'middle_name',
        'address',
        'gender',
        'user_id'
    ];

    protected $appends = ['user_avatar', 'full_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class);
    }

    public function getUserAvatarAttribute()
    {
        return $this->avatar ?? '/assets/images/default_avatar.png';
    }

    public function getFullNameAttribute()
    {
        if (!$this?->first_name || !$this?->last_name) {
            return "Unknown";
        }

        return "{$this->first_name} {$this->last_name}";
    }
}
