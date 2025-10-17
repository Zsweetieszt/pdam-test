<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    protected $fillable = [
        'meter_id',
        'billing_period_id',
        'bill_number',
        'previous_reading',
        'current_reading',
        'base_amount',
        'additional_charges',
        'tax_amount',
        'status',
        'issued_date',
        'due_date',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'issued_date' => 'date',
        'due_date' => 'date',
    ];

    public function meter(): BelongsTo
    {
        return $this->belongsTo(Meter::class);
    }

    public function billingPeriod(): BelongsTo
    {
        return $this->belongsTo(BillingPeriod::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Computed attributes
    public function getUsageM3Attribute(): int
    {
        return $this->current_reading - $this->previous_reading;
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->base_amount + $this->additional_charges + $this->tax_amount;
    }
}
