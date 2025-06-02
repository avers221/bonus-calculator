<?php

namespace App\DTO\Calculator;

class ConditionDTO
{
    public function __construct(
        public ?string $field,
        public ?string $operator,
        public ?string $value,
    ){}
}
