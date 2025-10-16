<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meter extends Model
{
    protected $fillable = [
        'customer_id',
        'meter_number',
        'meter_type',
        'customer_group_code',
        'customer_group_name',
        'meter_size',
        'block1_rate',
        'block2_rate',
        'block3_rate',
        'block4_rate',
        'admin_fee',
        'block1_limit',
        'block2_limit',
        'block3_limit',
        'installation_date',
        'is_active',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'is_active' => 'boolean',
        'block1_rate' => 'decimal:2',
        'block2_rate' => 'decimal:2',
        'block3_rate' => 'decimal:2',
        'block4_rate' => 'decimal:2',
        'admin_fee' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_code', 'code');
    }

    /**
     * Get the current outstanding bill for this meter
     */
    public function getOutstandingBillAttribute()
    {
        return $this->bills()
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->first();
    }

    /**
     * Get total outstanding amount for this meter
     */
    public function getTotalOutstandingAttribute(): float
    {
        return $this->bills()
            ->where('status', 'unpaid')
            ->sum('total_amount');
    }

    /**
     * Get last reading for this meter
     */
    public function getLastReadingAttribute(): int
    {
        $lastBill = $this->bills()
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $lastBill ? $lastBill->current_reading : 0;
    }

    /**
     * Calculate bill for this meter based on usage
     * 
     * @param int $currentReading Current meter reading
     * @param int $previousReading Previous meter reading (optional, will use last bill if not provided)
     * @return array Calculation details
     */
    public function calculateBill(int $currentReading, int $previousReading = null): array
    {
        if ($previousReading === null) {
            $previousReading = $this->last_reading;
        }

        $usage = $currentReading - $previousReading;
        
        if ($usage < 0) {
            throw new \InvalidArgumentException('Current reading cannot be less than previous reading');
        }

        $customerGroup = $this->customerGroup;
        if (!$customerGroup) {
            throw new \InvalidArgumentException('Customer group not found for meter');
        }

        return $customerGroup->calculateBill($usage, $this->meter_size);
    }

    /**
     * Set tariff rates from customer group
     * This method is called when meter is created or customer group is changed
     */
    public function updateTariffFromGroup(): void
    {
        $customerGroup = CustomerGroup::where('code', $this->customer_group_code)->first();
        if (!$customerGroup) {
            throw new \InvalidArgumentException('Invalid customer group code');
        }

        $adminFee = MeterAdminFee::getFeeBySize($this->meter_size);

        $this->update([
            'customer_group_name' => $customerGroup->name,
            'block1_rate' => $customerGroup->block1_rate,
            'block2_rate' => $customerGroup->block2_rate,
            'block3_rate' => $customerGroup->block3_rate,
            'block4_rate' => $customerGroup->block4_rate,
            'admin_fee' => $adminFee,
            'block1_limit' => $customerGroup->block1_limit,
            'block2_limit' => $customerGroup->block2_limit,
            'block3_limit' => $customerGroup->block3_limit,
        ]);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-update tariff when meter is created
        static::creating(function ($meter) {
            if ($meter->customer_group_code && $meter->meter_size) {
                $customerGroup = CustomerGroup::where('code', $meter->customer_group_code)->first();
                if ($customerGroup) {
                    $adminFee = MeterAdminFee::getFeeBySize($meter->meter_size);
                    
                    $meter->customer_group_name = $customerGroup->name;
                    $meter->block1_rate = $customerGroup->block1_rate;
                    $meter->block2_rate = $customerGroup->block2_rate;
                    $meter->block3_rate = $customerGroup->block3_rate;
                    $meter->block4_rate = $customerGroup->block4_rate;
                    $meter->admin_fee = $adminFee;
                    $meter->block1_limit = $customerGroup->block1_limit;
                    $meter->block2_limit = $customerGroup->block2_limit;
                    $meter->block3_limit = $customerGroup->block3_limit;
                }
            }
        });
    }
}
