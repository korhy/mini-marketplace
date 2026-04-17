<?php

declare(strict_types=1);

namespace App\Order\Infrastructure\Adapter;

use App\Listing\Domain\Repository\ListingRepositoryInterface;
use App\Listing\Domain\ValueObject\ListingStatus;
use App\Order\Domain\Port\ListingAvailabilityChecker;
use App\Shared\Domain\ValueObject\ListingId;

final class DoctrineListingAvailabilityChecker implements ListingAvailabilityChecker
{
    public function __construct(private ListingRepositoryInterface $listingRepository)
    {
    }

    public function isAvailable(ListingId $listingId): bool
    {
        $listing = $this->listingRepository->findById($listingId);

        return null !== $listing && ListingStatus::PUBLISHED === $listing->status();
    }
}
