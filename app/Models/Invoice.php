<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\Packingslip;

class Invoice extends Model
{
    //
    /**
     * Get the customer that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * Get the sales order that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sales_order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }

    /**
     * Get the packingslip that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function packingslip(): BelongsTo
    {
        return $this->belongsTo(Packingslip::class, 'packingslip_id', 'id');
    }
}
