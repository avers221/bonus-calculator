<?php

namespace App\DTO\Calculator;

use App\Models\Formula;

class FormulaDTO
{
    public function __construct(
        public ?string $operation,
        public mixed $value,
    ){}

    public static function fromModel(?Formula $formula): ?FormulaDTO
    {
        if (!$formula) {
            return null;
        }

        return new self(
            $formula->operation,
            $formula->value
        );
    }
}
