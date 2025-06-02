<?php

namespace App\Services;

use App\DTO\CalculateBonusDTO;
use App\DTO\CalculatedResultDTO;
use App\DTO\Calculator\FormulaDTO;
use App\Enums\ConditionOperation;
use App\Enums\FormulaOperation;
use App\Models\Condition;
use App\Repositories\BonusRuleRepository;
use DateTime;
use isDayOff\Client\IsDayOff;

class BonusCalculatorService
{
    public function __construct(
        private readonly IsDayOff $dayOff,
        private readonly BonusRuleRepository $ruleRepository,
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
        $fromOldStepBonus = 0;
        $appliedRules = [];

        // Применение правил по статусу клиента
        foreach ($rules as $rule) {
            // Проверяем, применимо ли правило для текущего клиента
            if ($this->evaluateCondition(
                $rule->condition,
                [
                    'date' => $date,
                    'customer_status' => $dto->status,
                    ]
            )) {
                // Применяем формулу, указанную в правиле
                $calculatedBonus = $this->applyFormula($calculatedBonus, new FormulaDTO(
                    $rule->formula['operation'],
                    $rule->formula['value'],
                ));
                $appliedRules[] = ['rule' => $rule->slug, 'bonus' => $calculatedBonus - $fromOldStepBonus];
                $fromOldStepBonus = $calculatedBonus;
            }
        }

        return new CalculatedResultDTO(round($calculatedBonus), $appliedRules);
    }

    private function evaluateCondition(?Condition $condition, array $context): bool
    {
        if (empty($condition)) {
            return true;
        }

        $field = $condition->field ?? null;
        $operator = $condition->operator ?? null;
        $value = $condition->value ?? null;

        if (!$field || !$operator) {
            return false;
        }

        $actualValue = $context[$field] ?? null;

        return match ($operator) {
            ConditionOperation::EQUAL->value => $actualValue == $value,
            ConditionOperation::NOT_EQUAL->value => $actualValue != $value,
            ConditionOperation::IN->value => in_array($actualValue, $context[$value] ?? []),
            ConditionOperation::NOT_IN->value => !in_array($actualValue, $context[$value] ?? []),
            ConditionOperation::CHECK_HOLIDAY->value => $this->dayOff->date()->isDayOff($context['date']) ?? false,
            ConditionOperation::CHECK_STATUS->value => $condition['value'] == $context[$value],
            default => false,
        };
    }

    private function applyFormula(float $bonus, FormulaDTO $formula): float
    {
        return match ($formula->operation) {
            FormulaOperation::MULTIPLY->value => $bonus * $formula->value,
            FormulaOperation::ADD->value => $bonus + $formula->value,
            FormulaOperation::DIVIDE->value => $bonus / $formula->value,
            FormulaOperation::DIVISION_WITHOUT_REMAINDER->value => intdiv($bonus, $formula->value),
            FormulaOperation::SUBTRACT->value => $bonus - $formula->value,
            default => $bonus,
        };
    }
}
