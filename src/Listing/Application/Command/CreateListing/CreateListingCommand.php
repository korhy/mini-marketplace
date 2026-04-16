<?php

declare(strict_types=1);

namespace App\Listing\Application\Command\CreateListing;

final readonly class CreateListingCommand
{
    public function __construct(
        public string $sellerId,
        public string $title,
        public string $description,
        public int $price,
        public string $currency,
        public string $condition,
    ) {
    }
}
