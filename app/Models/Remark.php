<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    use HasFactory;

    protected $fillable = [
        'remarkable_id',
        'remarkable_type',
        'user_id',
        'content'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function remarkable()
    {
        return $this->morphTo();
    }
}
