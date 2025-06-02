<?php

namespace App\DTO\Calculator;

use App\Models\Condition;

class ConditionDTO
{
    public function __construct(
        public ?string $field,
        public ?string $operator,
        public ?string $value,
    ){}

    public static function fromModel(?Condition $condition): ?ConditionDTO
    {
        if (!$condition) {
            return null;
        }

        return new self(
            $condition->field,
            $condition->operator,
            $condition->value
        );
    }
}
