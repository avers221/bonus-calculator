<?php

namespace Tests\Unit;

use App\DTO\CalculateBonusDTO;
use App\DTO\CalculatedResultDTO;
use App\Enums\CustomerStatus;
use App\Repositories\BonusRuleRepository;
use App\Services\BonusCalculator\BonusCalculatorService;
use App\Services\BonusCalculator\ConditionEvaluator;
use App\Services\BonusCalculator\FormulaApplier;
use App\Services\Holiday\NullHolidayChecker;
use DateTimeInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BonusCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private BonusCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Прогон сидера с бонусными правилами
        $this->seed(\Database\Seeders\BonusRulesSeeder::class);

        // Используем стаб для выходного дня (всегда рабочий день)
        $holidayChecker = new NullHolidayChecker();

        $this->service = new BonusCalculatorService(
            new BonusRuleRepository(),
            new ConditionEvaluator($holidayChecker),
            new FormulaApplier()
        );
    }

    /** @test */
    public function it_applies_base_rule_only_for_regular_user_on_weekday(): void
    {
        $dto = new CalculateBonusDTO(
            transaction_amount: 105.0,
            date: now()->format('Y-m-d'),
            status: CustomerStatus::REGULAR->value
        );

        $result = $this->service->calculateBonus($dto);

        $this->assertInstanceOf(CalculatedResultDTO::class, $result);
        $this->assertEquals(10, $result->totalBonus); // 105 / 10 = 10 (intdiv)
        $this->assertCount(1, $result->appliedRules);
        $this->assertEquals('base_rate', $result->appliedRules[0]['rule']);
    }

    /** @test */
    public function it_applies_all_rules_for_vip_user_on_weekend(): void
    {
        // Замена checker на выходной (через контейнер)
        $this->app->bind(
            \App\Services\Holiday\HolidayChecker::class,
            fn () => new class implements \App\Services\Holiday\HolidayChecker {
                public function isHoliday(DateTimeInterface $date): bool
                {
                    return true;
                }
            }
        );

        // Пересоздаем сервис с новым checker
        $this->service = new BonusCalculatorService(
            new BonusRuleRepository(),
            new ConditionEvaluator(app(\App\Services\Holiday\HolidayChecker::class)),
            new FormulaApplier()
        );

        $dto = new CalculateBonusDTO(
            transaction_amount: 100.0,
            date: now()->format('Y-m-d'),
            status: CustomerStatus::VIP->value
        );

        $result = $this->service->calculateBonus($dto);

        // Правила:
        // 1. base_rate: 100 // 10 = 10
        // 2. holiday_bonus: 10 * 2 = 20
        // 3. vip_boost: 20 * 1.4 = 28
        $this->assertEquals(28.0, $result->totalBonus);
        $this->assertCount(3, $result->appliedRules);
        $this->assertEqualsCanonicalizing(
            ['base_rate', 'holiday_bonus', 'vip_boost'],
            array_column($result->appliedRules, 'rule')
        );
    }

    /** @test */
    public function it_skips_vip_bonus_for_regular_user(): void
    {
        $dto = new CalculateBonusDTO(
            transaction_amount: 100.0,
            date: now()->format('Y-m-d'),
            status: CustomerStatus::REGULAR->value
        );

        $result = $this->service->calculateBonus($dto);

        // Только base_rate должен сработать
        $this->assertEquals(10.0, $result->totalBonus);
        $this->assertCount(1, $result->appliedRules);
        $this->assertEquals('base_rate', $result->appliedRules[0]['rule']);
    }

    /** @test */
    public function it_rounds_final_bonus_result(): void
    {
        $dto = new CalculateBonusDTO(
            transaction_amount: 99.9,
            date: now()->format('Y-m-d'),
            status: CustomerStatus::REGULAR->value
        );

        $result = $this->service->calculateBonus($dto);

        $this->assertEquals(9, $result->totalBonus);
    }
}
