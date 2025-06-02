<?php

namespace App\Repositories;

use App\Models\BonusRule;

class BonusRuleRepository
{
    public function getAllOrdered(): \Illuminate\Support\Collection
    {
        return BonusRule::query()->orderBy('priority')->get();
    }
}
