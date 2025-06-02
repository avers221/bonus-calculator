<?php

namespace App\Services\BonusCalculator;

use App\DTO\Calculator\FormulaDTO;
use App\Enums\FormulaOperation;

class FormulaApplier
{
    public function apply(FormulaDTO $formula, float $bonus): float
    {
        return match ($formula->operation) {
            FormulaOperation::MULTIPLY->value => $bonus * $formula->value,
            FormulaOperation::ADD->value => $bonus + $formula->value,
            FormulaOperation::DIVIDE->value => $this->safeDivide($bonus, $formula->value),
            FormulaOperation::DIVISION_WITHOUT_REMAINDER->value => $this->safeIntDiv($bonus, $formula->value),
            FormulaOperation::SUBTRACT->value => $bonus - $formula->value,
            default => $bonus,
        };
    }

    private function safeDivide(float $a, float $b): float
    {
        if ($b == 0.0) {
            throw new \InvalidArgumentException('Division by zero in formula');
        }

        return $a / $b;
    }

    private function safeIntDiv(float $a, float $b): int
    {
        if ($b == 0.0) {
            throw new \InvalidArgumentException('Division by zero in formula');
        }

        return intdiv((int)$a, (int)$b);
    }

}
