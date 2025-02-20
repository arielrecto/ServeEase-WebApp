<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceType extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'thumbnail',
        'description'
    ];

    /**
     * Get all of the services for the ServiceType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

}
