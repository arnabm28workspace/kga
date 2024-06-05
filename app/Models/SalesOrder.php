<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Customer;
use App\User;

class SalesOrder extends Model
{
    // use HasFactory;
    /**
     * Get the customer that owns the SalesOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id', 'id');
    }

    /**
     * Get the user that owns the SalesOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }
}
