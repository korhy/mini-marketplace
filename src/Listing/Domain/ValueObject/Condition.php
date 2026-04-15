<?php

declare(strict_types=1);

namespace App\Listing\Domain\ValueObject;

enum Condition: string
{
    case NEW = 'new';
    case GOOD = 'good';
    case USED = 'used';
}
