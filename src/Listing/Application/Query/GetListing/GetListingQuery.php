<?php

declare(strict_types=1);

namespace App\Listing\Application\Query\GetListing;

final readonly class GetListingQuery
{
    public function __construct(
        public string $listingId,
    ) {
    }
}
