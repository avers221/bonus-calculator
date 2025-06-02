<?php

namespace App\Services\BonusCalculator;

use App\DTO\CalculateBonusDTO;
use App\DTO\CalculatedResultDTO;
use App\DTO\Calculator\ConditionDTO;
use App\DTO\Calculator\FormulaDTO;
use App\Repositories\BonusRuleRepository;
use DateTime;

class BonusCalculatorService
{
    public function __construct(
        private readonly BonusRuleRepository $ruleRepository,
        private readonly ConditionEvaluator $conditionEvaluator,
        private readonly FormulaApplier $formulaApplier,
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function calculateBonus(CalculateBonusDTO $dto): CalculatedResultDTO
    {
        $date = new DateTime($dto->date);

        // Загружаем правила из базы
        $rules = $this->ruleRepository->getAllOrdered();

        $calculatedBonus = $dto->transaction_amount;
        $appliedRules = [];
        $prevBonus = 0;

        $context = [
            'date' => $date,
            'customer_status' => $dto->status,
        ];

        // Применение правил по статусу клиента
        foreach ($rules as $rule) {
            // Проверяем, применимо ли правило для текущего клиента
            if (
                !$this->conditionEvaluator->passes(
                ConditionDTO::fromModel($rule->condition),
                $context,
            )
            ) {
                continue;
            }

            // Применяем формулу, указанную в правиле
            $calculatedBonus = $this->formulaApplier->apply(
                FormulaDTO::fromModel($rule->formula),
                $calculatedBonus,
            );

            $appliedRules[] = ['rule' => $rule->slug, 'bonus' => $calculatedBonus - $prevBonus];
            $prevBonus = $calculatedBonus;

        }

        return new CalculatedResultDTO(round($calculatedBonus), $appliedRules);
    }
}
