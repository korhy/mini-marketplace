<?php

declare(strict_types=1);

namespace App\Order\Domain\Event;

use App\Order\Domain\ValueObject\OrderId;
use App\Shared\Domain\Events\DomainEvent;

final class OrderCancelled extends DomainEvent
{
    public function __construct(
        public readonly OrderId $orderId,
    ) {
        parent::__construct();
    }
}
