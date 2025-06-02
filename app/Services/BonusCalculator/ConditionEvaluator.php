<?php

namespace App\Services\BonusCalculator;

use App\DTO\Calculator\ConditionDTO;
use App\Enums\ConditionOperation;
use App\Services\Holiday\HolidayChecker;

class ConditionEvaluator
{
    public function __construct
    (
        private readonly HolidayChecker $holidayChecker
    ){}

    public function passes(?ConditionDTO $condition, array $context): bool
    {
        if (empty($condition) || !$condition->field || !$condition->operator) {
            return true;
        }

        $actualValue = $context[$condition->field] ?? null;

        return match ($condition->operator) {
            ConditionOperation::EQUAL->value => $actualValue == $condition->value,
            ConditionOperation::NOT_EQUAL->value => $actualValue != $condition->value,
            ConditionOperation::IN->value => in_array($actualValue, (array) $context[$condition->value] ?? []),
            ConditionOperation::NOT_IN->value => !in_array($actualValue, (array) $context[$condition->value] ?? []),
            ConditionOperation::CHECK_HOLIDAY->value => $this->holidayChecker->isHoliday($context['date']),
            ConditionOperation::CHECK_STATUS->value => $condition->value == $actualValue,
            default => false,
        };
    }
}
