<?php

declare(strict_types=1);

namespace App\Order\Application\Query\GetOrder;

final readonly class OrderViewModel
{
    public function __construct(
        public string $id,
        public string $buyerId,
        public string $listingId,
        public int $totalPrice,
        public string $currency,
        public string $status,
    ) {
    }
}
