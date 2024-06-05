<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ServicePartner extends Model
{
    // use HasFactory;

   

    /**
     * Get all of the comments for the ServicePartner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pincodes(): HasMany
    {
        return $this->hasMany(\App\Models\ServicePartnerPincode::class);
    }

}
