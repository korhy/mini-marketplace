<?php

declare(strict_types=1);

namespace App\Listing\Domain\Exception;

final class ListingNotFoundException extends \DomainException
{
    public static function forId(string $listingId): self
    {
        return new self("Listing not found: $listingId");
    }
}
