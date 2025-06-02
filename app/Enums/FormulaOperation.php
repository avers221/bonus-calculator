<?php

namespace App\Enums;

enum FormulaOperation: string
{
    case MULTIPLY = 'multiply';
    case ADD = 'add';
    case DIVIDE = 'divide';
    case SUBTRACT = 'subtract';
    case DIVISION_WITHOUT_REMAINDER = 'division_without_remainder';
}
