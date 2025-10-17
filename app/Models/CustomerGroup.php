<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerGroup extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'block1_rate',
        'block2_rate',
        'block3_rate',
        'block4_rate',
        'block1_limit',
        'block2_limit',
        'block3_limit',
        'is_active',
    ];

    protected $casts = [
        'block1_rate' => 'decimal:2',
        'block2_rate' => 'decimal:2',
        'block3_rate' => 'decimal:2',
        'block4_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class, 'customer_group_code', 'code');
    }

    /**
     * Calculate bill amount based on usage and tariff structure
     * 
     * @param int $usage Usage in m3
     * @param string $meterSize Meter size for admin fee
     * @return array
     */
    public function calculateBill(int $usage, string $meterSize): array
    {
        $calculation = [
            'usage_m3' => $usage,
            'blocks' => [],
            'water_charge' => 0,
            'admin_fee' => 0,
            'total_amount' => 0,
        ];

        // Get admin fee based on meter size
        $adminFee = MeterAdminFee::where('meter_size', $meterSize)->first();
        $calculation['admin_fee'] = $adminFee ? $adminFee->admin_fee : 0;

        $remainingUsage = $usage;
        $blocks = [
            ['limit' => $this->block1_limit, 'rate' => $this->block1_rate, 'name' => 'Blok I'],
            ['limit' => $this->block2_limit, 'rate' => $this->block2_rate, 'name' => 'Blok II'],
            ['limit' => $this->block3_limit, 'rate' => $this->block3_rate, 'name' => 'Blok III'],
            ['limit' => 999999, 'rate' => $this->block4_rate, 'name' => 'Blok IV'], // Unlimited for block 4
        ];

        foreach ($blocks as $blockIndex => $block) {
            if ($remainingUsage <= 0) break;
            if ($block['rate'] == 0 && $blockIndex == 3) break; // Skip block 4 if rate is 0

            $blockUsage = min($remainingUsage, $block['limit']);
            $blockAmount = $blockUsage * $block['rate'];

            $calculation['blocks'][] = [
                'name' => $block['name'],
                'usage' => $blockUsage,
                'rate' => $block['rate'],
                'amount' => $blockAmount,
            ];

            $calculation['water_charge'] += $blockAmount;
            $remainingUsage -= $blockUsage;
        }

        $calculation['total_amount'] = $calculation['water_charge'] + $calculation['admin_fee'];

        return $calculation;
    }

    /**
     * Get available customer groups for dropdown
     */
    public static function getForDropdown(): array
    {
        return self::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($group) {
                return [
                    'value' => $group->code,
                    'label' => $group->code . ' - ' . $group->name,
                    'category' => $group->category,
                ];
            })
            ->toArray();
    }
}
