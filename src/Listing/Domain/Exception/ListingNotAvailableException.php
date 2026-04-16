<?php

declare(strict_types=1);

namespace App\Listing\Domain\Exception;

final class ListingNotAvailableException extends \DomainException
{
    public static function forListing(string $listingId): self
    {
        return new self(sprintf('Listing "%s" is not available.', $listingId));
    }
}
