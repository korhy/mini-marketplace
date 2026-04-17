<?php

declare(strict_types=1);

namespace App\Order\Application\Command\PlaceOrder;

final readonly class PlaceOrderCommand
{
    public function __construct(
        public string $buyerId,
        public string $listingId,
        public int $price,
        public string $currency,
    ) {
    }
}
