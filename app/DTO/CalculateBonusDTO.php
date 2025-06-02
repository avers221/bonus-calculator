<?php

namespace App\DTO;

class CalculateBonusDTO
{
    public function __construct(
        public float $transaction_amount,
        public string $date,
        public string $status,
    ){}
}
