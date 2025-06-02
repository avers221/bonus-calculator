<?php

namespace App\Services\Holiday;

use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use isDayOff\Client\IsDayOff;
use Psr\Log\LoggerInterface;

class IsDaysOffHolidayChecker implements HolidayChecker
{
    public function __construct(
        private readonly IsDayOff $client,
        private readonly LoggerInterface $logger,
        private readonly int $ttlMinutes = 1440 // 1 день
    ) {}


    public function isHoliday(DateTimeInterface $date): bool
    {
        $key = 'holiday:' . $date->format('Y-m-d');

        return Cache::remember($key, now()->addMinutes($this->ttlMinutes), function () use($date) {
            try {
                return $this->client->date()->isDayOff($date);
            } catch (\Throwable $e) {
                $this->logger->warning('[HolidayChecker] Ошибка запроса к isDayOff', [
                    'date' => $date->format('Y-m-d'),
                    'error' => $e->getMessage()
                ]);
            }
            return false; // считаем, что это не выходной
        });
    }
}
