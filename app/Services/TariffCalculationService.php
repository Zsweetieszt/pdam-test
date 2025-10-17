<?php

namespace App\Services;

use App\Models\CustomerGroup;
use App\Models\MeterAdminFee;
use App\Models\Meter;
use App\Models\Bill;
use App\Models\BillingPeriod;

class TariffCalculationService
{
    /**
     * Calculate bill amount based on meter and usage
     * Following the tariff structure from Kepbup
     * 
     * Example calculation based on image:
     * 2R1 - Rumah Tinggal Menengah, wm 1/2", Usage: 30 m3
     * - Blok I: 10 m3 x 7,100 = 71,000
     * - Blok II: 10 m3 x 8,500 = 85,000  
     * - Blok III: 10 m3 x 9,500 = 95,000
     * - Uang Air: 251,000
     * - Biaya Administrasi: 7,500
     * - Total: 258,500
     */
    public function calculateBill(Meter $meter, int $currentReading, int $previousReading = null): array
    {
        if ($previousReading === null) {
            $previousReading = $meter->last_reading;
        }

        $usage = $currentReading - $previousReading;
        
        if ($usage < 0) {
            throw new \InvalidArgumentException('Pembacaan meter saat ini tidak boleh kurang dari pembacaan sebelumnya');
        }

        // Get customer group data
        $customerGroup = $meter->customerGroup;
        if (!$customerGroup) {
            throw new \InvalidArgumentException('Golongan pelanggan tidak ditemukan untuk meter ini');
        }

        $calculation = [
            'meter_id' => $meter->id,
            'meter_number' => $meter->meter_number,
            'customer_group' => [
                'code' => $customerGroup->code,
                'name' => $customerGroup->name,
            ],
            'meter_size' => $meter->meter_size,
            'readings' => [
                'previous' => $previousReading,
                'current' => $currentReading,
                'usage_m3' => $usage,
            ],
            'blocks' => [],
            'water_charge' => 0,
            'admin_fee' => $meter->admin_fee,
            'total_amount' => 0,
        ];

        // Calculate block-wise charges using customer group rates
        $remainingUsage = $usage;
        $blocks = [
            [
                'name' => 'Blok I',
                'limit' => $customerGroup->block1_limit,
                'rate' => $customerGroup->block1_rate,
            ],
            [
                'name' => 'Blok II', 
                'limit' => $customerGroup->block2_limit,
                'rate' => $customerGroup->block2_rate,
            ],
            [
                'name' => 'Blok III',
                'limit' => $customerGroup->block3_limit,
                'rate' => $customerGroup->block3_rate,
            ],
            [
                'name' => 'Blok IV',
                'limit' => 999999, // Unlimited
                'rate' => $customerGroup->block4_rate,
            ],
        ];

        foreach ($blocks as $block) {
            if ($remainingUsage <= 0) break;
            if ($block['rate'] == 0 && $block['name'] == 'Blok IV') break; // Skip block 4 if rate is 0

            $blockUsage = min($remainingUsage, $block['limit']);
            $blockAmount = $blockUsage * $block['rate'];

            if ($blockUsage > 0) {
                $calculation['blocks'][] = [
                    'name' => $block['name'],
                    'limit' => $block['limit'],
                    'usage' => $blockUsage,
                    'rate' => $block['rate'],
                    'amount' => $blockAmount,
                ];

                $calculation['water_charge'] += $blockAmount;
                $remainingUsage -= $blockUsage;
            }
        }

        $calculation['total_amount'] = $calculation['water_charge'] + $calculation['admin_fee'];

        return $calculation;
    }

    /**
     * Generate bill for a meter
     */
    public function generateBill(Meter $meter, int $currentReading, BillingPeriod $billingPeriod): Bill
    {
        $calculation = $this->calculateBill($meter, $currentReading);

        $bill = new Bill([
            'meter_id' => $meter->id,
            'billing_period_id' => $billingPeriod->id,
            'bill_number' => $this->generateBillNumber($meter, $billingPeriod),
            'previous_reading' => $calculation['readings']['previous'],
            'current_reading' => $calculation['readings']['current'],
            'base_amount' => $calculation['water_charge'],
            'additional_charges' => $calculation['admin_fee'],
            'tax_amount' => 0, // Tax calculation if needed
            'status' => 'unpaid',
            'issued_date' => now(),
            'due_date' => now()->addDays(30), // 30 days payment period
        ]);

        $bill->save();

        return $bill;
    }

    /**
     * Generate unique bill number
     */
    private function generateBillNumber(Meter $meter, BillingPeriod $billingPeriod): string
    {
        $year = $billingPeriod->year;
        $month = str_pad($billingPeriod->month, 2, '0', STR_PAD_LEFT);
        $meterCode = str_pad($meter->id, 6, '0', STR_PAD_LEFT);
        
        return "BILL-{$year}{$month}-{$meterCode}";
    }

    /**
     * Get tariff simulation for frontend
     */
    public function simulateTariff(string $customerGroupCode, string $meterSize, int $usage): array
    {
        $customerGroup = CustomerGroup::where('code', $customerGroupCode)->first();
        if (!$customerGroup) {
            throw new \InvalidArgumentException('Golongan pelanggan tidak ditemukan');
        }

        // Use CustomerGroup calculateBill method
        $calculation = $customerGroup->calculateBill($usage, $meterSize);
        
        // Add customer group info
        $calculation['customer_group'] = [
            'code' => $customerGroup->code,
            'name' => $customerGroup->name,
            'category' => $customerGroup->category,
        ];
        
        $calculation['meter_size'] = $meterSize;
        
        return $calculation;
    }

    /**
     * Get available customer groups organized by category
     */
    public function getCustomerGroupsByCategory(): array
    {
        $groups = CustomerGroup::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->groupBy('category');

        $result = [];
        foreach ($groups as $category => $categoryGroups) {
            $result[$category] = $categoryGroups->map(function ($group) {
                return [
                    'code' => $group->code,
                    'name' => $group->name,
                    'label' => $group->code . ' - ' . $group->name,
                    'rates' => [
                        'block1' => $group->block1_rate,
                        'block2' => $group->block2_rate,
                        'block3' => $group->block3_rate,
                        'block4' => $group->block4_rate,
                    ],
                ];
            })->toArray();
        }

        return $result;
    }

    /**
     * Get meter sizes with admin fees
     */
    public function getMeterSizes(): array
    {
        return MeterAdminFee::getForDropdown();
    }
}
