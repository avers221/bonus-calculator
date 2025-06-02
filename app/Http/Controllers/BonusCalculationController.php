<?php

namespace App\Http\Controllers;

use App\DTO\CalculateBonusDTO;
use App\Http\Requests\BonusCalculationRequest;
use App\Services\BonusCalculatorService;
use DateTime;
use isDayOff\Client\IsDayOff;

class BonusCalculationController extends Controller
{
    public function __construct(private BonusCalculatorService $service)
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(BonusCalculationRequest $request)
    {
        $validated = $request->validated();

        $calculatedData = $this->service->calculateBonus(
            new CalculateBonusDTO(
                $validated['transaction_amount'],
                $validated['timestamp'],
                $validated['customer_status']
            )
        );

        return response()->json($calculatedData);
    }
}
