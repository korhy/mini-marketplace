<?php

declare(strict_types=1);

namespace App\Shared\Domain\IntegrationEvent;

use App\Shared\Domain\Events\DomainEvent;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Domain\ValueObject\UserId;

final class OrderConfirmedIntegrationEvent extends DomainEvent
{
    public function __construct(
        public readonly ListingId $listingId,
        public readonly UserId $buyerId,
        public readonly Money $totalPrice,
    ) {
        parent::__construct();
    }
}
