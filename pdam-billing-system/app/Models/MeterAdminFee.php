<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterAdminFee extends Model
{
    protected $fillable = [
        'meter_size',
        'admin_fee',
        'is_active',
    ];

    protected $casts = [
        'admin_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get available meter sizes for dropdown
     */
    public static function getForDropdown(): array
    {
        return self::where('is_active', true)
            ->orderBy('meter_size')
            ->get()
            ->map(function ($size) {
                return [
                    'value' => $size->meter_size,
                    'label' => 'wm ' . $size->meter_size,
                    'admin_fee' => $size->admin_fee,
                ];
            })
            ->toArray();
    }

    /**
     * Get admin fee by meter size
     */
    public static function getFeeBySize(string $meterSize): float
    {
        $fee = self::where('meter_size', $meterSize)
            ->where('is_active', true)
            ->first();
            
        return $fee ? $fee->admin_fee : 0;
    }
}
