<?php

namespace App\Services\Holiday;

use DateTimeInterface;

class NullHolidayChecker implements HolidayChecker
{
    public function isHoliday(DateTimeInterface $date): bool
    {
        return false;
    }
}
