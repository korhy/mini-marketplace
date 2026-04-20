<?php

declare(strict_types=1);

namespace App\Order\Domain\Exception;

final class ListingNotAvailableForOrderException extends \DomainException
{
    public static function forListing(string $listingId): self
    {
        return new self(sprintf('Listing "%s" is not available for order.', $listingId));
    }
}
