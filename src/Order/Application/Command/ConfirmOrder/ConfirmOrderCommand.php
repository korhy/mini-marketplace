<?php

declare(strict_types=1);

namespace App\Order\Application\Command\ConfirmOrder;

final readonly class ConfirmOrderCommand
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
