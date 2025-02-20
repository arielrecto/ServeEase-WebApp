<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
