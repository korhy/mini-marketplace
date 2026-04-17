<?php

declare(strict_types=1);

namespace App\Order\Domain\Event;

use App\Order\Domain\ValueObject\BuyerId;
use App\Order\Domain\ValueObject\OrderId;
use App\Shared\Domain\Events\DomainEvent;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;

final class OrderPlaced extends DomainEvent
{
    public function __construct(
        public readonly OrderId $orderId,
        public readonly BuyerId $buyerId,
        public readonly ListingId $listingId,
        public readonly Money $totalPrice,
    ) {
        parent::__construct();
    }
}
