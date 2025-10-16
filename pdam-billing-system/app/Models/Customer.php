<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'customer_number',
        'ktp_number',
        'address',
        // tariff_group removed - now handled per meter
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }

    /**
     * Get all bills across all meters for this customer
     */
    public function allBills(): HasMany
    {
        return $this->hasManyThrough(Bill::class, Meter::class);
    }

    /**
     * Get all payments across all meters for this customer
     */
    public function allPayments(): HasMany
    {
        return $this->hasManyThrough(Payment::class, Bill::class, 'meter_id', 'bill_id', 'id', 'id')
            ->whereHas('bill.meter', function ($query) {
                $query->where('customer_id', $this->id);
            });
    }

    /**
     * Get outstanding bills across all meters
     */
    public function getOutstandingBillsAttribute()
    {
        return $this->allBills()
            ->where('status', 'unpaid')
            ->with(['meter', 'billingPeriod'])
            ->get();
    }

    /**
     * Get total outstanding amount across all meters
     */
    public function getTotalOutstandingAttribute(): float
    {
        return $this->outstandingBills->sum('total_amount');
    }

    /**
     * Check if customer can register new meter
     * Based on business rules (e.g., no outstanding bills, max meters, etc.)
     */
    public function canRegisterNewMeter(): bool
    {
        // Business rule: No outstanding bills
        if ($this->total_outstanding > 0) {
            return false;
        }

        // Business rule: Maximum 5 meters per customer (configurable)
        $maxMeters = config('app.max_meters_per_customer', 5);
        if ($this->meters()->count() >= $maxMeters) {
            return false;
        }

        return true;
    }
}
