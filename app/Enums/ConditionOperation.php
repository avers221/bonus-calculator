<?php

namespace App\Enums;

enum ConditionOperation: string
{
    case EQUAL = '=';
    case NOT_EQUAL = '!=';
    case IN = 'in';
    case NOT_IN = 'not_in';
    case CHECK_HOLIDAY = 'check_holiday';
    case CHECK_STATUS = 'check_status';
}
