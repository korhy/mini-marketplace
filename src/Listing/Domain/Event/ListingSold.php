<?php

declare(strict_types=1);

namespace App\Listing\Domain\Event;

use App\Listing\Domain\ValueObject\ListingId;
use App\Listing\Domain\ValueObject\SellerId;
use App\Shared\Domain\Events\DomainEvent;

final class ListingSold extends DomainEvent
{
    public function __construct(
        public readonly ListingId $listingId,
        public readonly SellerId $sellerId,
    ) {
        parent::__construct();
    }
}
