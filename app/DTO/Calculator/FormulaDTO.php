<?php

namespace App\DTO\Calculator;

class FormulaDTO
{
    public function __construct(
        public ?string $operation,
        public mixed $value,
    ){}
}
