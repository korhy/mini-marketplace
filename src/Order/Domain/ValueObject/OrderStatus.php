<?php

declare(strict_types=1);

namespace App\Order\Domain\ValueObject;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
}
