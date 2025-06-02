<?php

namespace App\Services\Holiday;

use DateTimeInterface;

interface HolidayChecker
{
    public function isHoliday(DateTimeInterface $date): bool;
}
