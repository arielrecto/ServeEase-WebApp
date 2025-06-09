<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    public function remarks()
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }

    public function latestRemark()
    {
        return $this->morphOne(Remark::class, 'remarkable')->latest();
    }


    protected $fillable = [
        'user_id',
        'complaint',
        'status',
        'type',
        'reported_by',
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
