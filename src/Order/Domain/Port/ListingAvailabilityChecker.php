<?php

declare(strict_types=1);

namespace App\Order\Domain\Port;

use App\Shared\Domain\ValueObject\ListingId;

interface ListingAvailabilityChecker
{
    public function isAvailable(ListingId $listingId): bool;
}
