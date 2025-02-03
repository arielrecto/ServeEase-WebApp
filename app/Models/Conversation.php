<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;


    protected $fillable = [
        'owner_id',
        'participant_id'
    ];



    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function participant(){
        return $this->belongsTo(User::class, 'participant_id');
    }


    public function messages(){
        return $this->hasMany(Message::class);
    }
}
