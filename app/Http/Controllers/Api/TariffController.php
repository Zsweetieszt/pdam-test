<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TariffCalculationService;
use App\Models\CustomerGroup;
use App\Models\MeterAdminFee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TariffController extends Controller
{
    protected $tariffService;

    public function __construct(TariffCalculationService $tariffService)
    {
        $this->tariffService = $tariffService;
    }

    /**
     * Get all customer groups organized by category
     * 
     * @return JsonResponse
     */
    public function getCustomerGroups(): JsonResponse
    {
        try {
            $groups = $this->tariffService->getCustomerGroupsByCategory();
            
            return response()->json([
                'success' => true,
                'data' => $groups,
                'message' => 'Customer groups retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customer groups: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all meter sizes with admin fees
     * 
     * @return JsonResponse
     */
    public function getMeterSizes(): JsonResponse
    {
        try {
            $sizes = $this->tariffService->getMeterSizes();
            
            return response()->json([
                'success' => true,
                'data' => $sizes,
                'message' => 'Meter sizes retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve meter sizes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simulate tariff calculation
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function simulateTariff(Request $request): JsonResponse
    {
        $request->validate([
            'customer_group_code' => 'required|string|exists:customer_groups,code',
            'meter_size' => 'required|string|exists:meter_admin_fees,meter_size',
            'usage' => 'required|integer|min:0|max:9999',
        ]);

        try {
            $calculation = $this->tariffService->simulateTariff(
                $request->customer_group_code,
                $request->meter_size,
                $request->usage
            );
            
            return response()->json([
                'success' => true,
                'data' => $calculation,
                'message' => 'Tariff calculation completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate tariff: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get detailed tariff information for a specific customer group
     * 
     * @param string $code
     * @return JsonResponse
     */
    public function getCustomerGroupDetail(string $code): JsonResponse
    {
        try {
            $group = CustomerGroup::where('code', $code)
                ->where('is_active', true)
                ->first();

            if (!$group) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer group not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'code' => $group->code,
                    'name' => $group->name,
                    'category' => $group->category,
                    'description' => $group->description,
                    'tariff_structure' => [
                        'blocks' => [
                            [
                                'name' => 'Blok I',
                                'limit' => $group->block1_limit,
                                'rate' => $group->block1_rate,
                                'description' => 'Pemakaian 0 - ' . $group->block1_limit . ' m³'
                            ],
                            [
                                'name' => 'Blok II',
                                'limit' => $group->block2_limit,
                                'rate' => $group->block2_rate,
                                'description' => 'Pemakaian ' . ($group->block1_limit + 1) . ' - ' . ($group->block1_limit + $group->block2_limit) . ' m³'
                            ],
                            [
                                'name' => 'Blok III',
                                'limit' => $group->block3_limit,
                                'rate' => $group->block3_rate,
                                'description' => 'Pemakaian ' . ($group->block1_limit + $group->block2_limit + 1) . ' - ' . ($group->block1_limit + $group->block2_limit + $group->block3_limit) . ' m³'
                            ],
                            [
                                'name' => 'Blok IV',
                                'limit' => 999999,
                                'rate' => $group->block4_rate,
                                'description' => 'Pemakaian > ' . ($group->block1_limit + $group->block2_limit + $group->block3_limit) . ' m³'
                            ]
                        ]
                    ]
                ],
                'message' => 'Customer group detail retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve customer group detail: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update customer group tariff (admin only)
     * 
     * @param Request $request
     * @param string $code
     * @return JsonResponse
     */
    public function updateCustomerGroup(Request $request, string $code): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'block1_rate' => 'required|numeric|min:0',
            'block2_rate' => 'required|numeric|min:0',
            'block3_rate' => 'required|numeric|min:0',
            'block4_rate' => 'required|numeric|min:0',
            'block1_limit' => 'required|integer|min:1',
            'block2_limit' => 'required|integer|min:1',
            'block3_limit' => 'required|integer|min:1',
        ]);

        try {
            $group = CustomerGroup::where('code', $code)->first();
            
            if (!$group) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer group not found'
                ], 404);
            }

            $group->update($request->only([
                'name', 'description', 'block1_rate', 'block2_rate', 
                'block3_rate', 'block4_rate', 'block1_limit', 
                'block2_limit', 'block3_limit'
            ]));

            return response()->json([
                'success' => true,
                'data' => $group,
                'message' => 'Customer group updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer group: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update meter admin fee (admin only)
     * 
     * @param Request $request
     * @param string $meterSize
     * @return JsonResponse
     */
    public function updateMeterAdminFee(Request $request, string $meterSize): JsonResponse
    {
        $request->validate([
            'admin_fee' => 'required|numeric|min:0',
        ]);

        try {
            $adminFee = MeterAdminFee::where('meter_size', $meterSize)->first();
            
            if (!$adminFee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meter size not found'
                ], 404);
            }

            $adminFee->update([
                'admin_fee' => $request->admin_fee
            ]);

            return response()->json([
                'success' => true,
                'data' => $adminFee,
                'message' => 'Meter admin fee updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update meter admin fee: ' . $e->getMessage()
            ], 500);
        }
    }
}
