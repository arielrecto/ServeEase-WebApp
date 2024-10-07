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



    public function user(){
        return $this->belongsTo(User::class);
    }


    public function providerProfile(){
        return $this->hasOne(ProviderProfile::class);
    }
}
