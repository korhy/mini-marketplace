<?php

declare(strict_types=1);

namespace App\Order\Application\Command\CancelOrder;

final readonly class CancelOrderCommand
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
