<?php

declare(strict_types=1);

namespace App\Listing\Application\Query\GetListing;

final readonly class ListingViewModel
{
    public function __construct(
        public string $id,
        public string $sellerId,
        public string $title,
        public string $description,
        public int $price,
        public string $currency,
        public string $condition,
        public string $status,
    ) {
    }
}
