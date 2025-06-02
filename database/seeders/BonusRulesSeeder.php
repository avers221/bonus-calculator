<?php

namespace Database\Seeders;

use App\Enums\ConditionOperation;
use App\Enums\CostumerStatus;
use App\Enums\FormulaOperation;
use App\Enums\WorkdayStatus;
use App\Models\BonusRule;
use App\Models\Condition;
use App\Models\Formula;
use Illuminate\Database\Seeder;

class BonusRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getRules() as $rule) {
            $bonusRule = BonusRule::updateOrCreate(
                [
                    'slug' => $rule['slug']
                ],
                [
                    'name' => $rule['name'],
                    'slug' => $rule['slug'],
                    'priority' => $rule['priority'],
                ]
            );

            if (empty($bonusRule->condition_id) && !empty($rule['condition'])) {
                $condition = Condition::create($rule['condition']);
                $bonusRule->condition_id = $condition->id;
            }

            if (empty($bonusRule->formula_id) && !empty($rule['formula'])) {
                $formula = Formula::create($rule['formula']);
                $bonusRule->formula_id = $formula->id;
            }
            $bonusRule->save();

        }
    }


    protected function getRules(): array
    {
        return [
            [
                'name' => 'Базовое правило',
                'slug' => 'base_rate',
                'condition' => [],
                'formula' => [
                    "operation" => FormulaOperation::DIVISION_WITHOUT_REMAINDER->value,
                    "value" => 10
                ],
                'priority' => 0,
            ],
            [
                'name' => 'Выходной день',
                'slug' => 'holiday_bonus',
                'condition' => [
                    "field" => "date",
                    "operator" => ConditionOperation::CHECK_HOLIDAY->value,
                    "value" => WorkdayStatus::NON_WORKDAY->value
                ],
                'formula' => [
                    "operation" => FormulaOperation::MULTIPLY->value,
                    "value" => 2
                ],
                'priority' => 1,
            ],
            [
                'name' => 'VIP-бонус',
                'slug' => 'vip_boost',
                'condition' => [
                    'field' => 'customer_status',
                    'operator' => ConditionOperation::EQUAL->value,
                    'value' => CostumerStatus::VIP->value,
                ],
                'formula' => [
                    'operation' => FormulaOperation::MULTIPLY->value,
                    'value' => 1.4,
                ],
                'priority' => 2,
            ],
        ];
    }
}
