<?php

declare(strict_types=1);

namespace App\Listing\Domain\Repository;

use App\Listing\Domain\Entity\Listing;
use App\Listing\Domain\ValueObject\ListingId;

interface ListingRepositoryInterface
{
    public function save(Listing $listing): void;

    public function findById(ListingId $id): ?Listing;
}