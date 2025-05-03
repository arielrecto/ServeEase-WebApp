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
        'experience_duration',
        'contact',
        'verified_at',
        'valid_id_type',
        'valid_id_image',
        'citizenship_document_type',
        'citizenship_document_image',
        'proof_document_image',
        'status',
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
    public function remarks()
    {
        return $this->morphMany(Remark::class, 'remarkable');
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
