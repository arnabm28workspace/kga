<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PurchaseOrderProduct;

class PurchaseOrder extends Model
{
    // use HasFactory;

    /**
     * Get the supplier that owns the PurchaseOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id', 'id');
    }

    /**
     * Get the stock associated with the PurchaseOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stock(): HasOne
    {
        return $this->hasOne(\App\Models\Stock::class, 'purchase_order_id', 'id');
    }

    /**
     * Get all of the purchase_order_products for the PurchaseOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function purchase_order_products(): HasMany
    {
        return $this->hasMany(\App\Models\PurchaseOrderProduct::class, 'purchase_order_id', 'id');
    }
}
