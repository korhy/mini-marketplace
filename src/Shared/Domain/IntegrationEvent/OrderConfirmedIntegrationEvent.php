<?php

declare(strict_types=1);

namespace App\Shared\Domain\IntegrationEvent;

use App\Shared\Domain\Events\DomainEvent;
use App\Shared\Domain\ValueObject\ListingId;

final class OrderConfirmedIntegrationEvent extends DomainEvent
{
    public function __construct(
        public readonly ListingId $listingId,
    ) {
        parent::__construct();
    }
}
