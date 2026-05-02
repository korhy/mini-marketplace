<?php

declare(strict_types=1);

namespace App\Listing\Infrastructure\Repository;

use App\Listing\Domain\Entity\Listing;
use App\Listing\Domain\Exception\ListingNotAvailableException;
use App\Listing\Domain\Repository\ListingRepositoryInterface;
use App\Shared\Domain\ValueObject\ListingId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;

final class DoctrineListingRepository implements ListingRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Listing $listing): void
    {
        try {
            $this->entityManager->persist($listing);
            $this->entityManager->flush();
        } catch (OptimisticLockException) {
            throw ListingNotAvailableException::forListing((string) $listing->id());
        }
    }

    public function findById(ListingId $id): ?Listing
    {
        return $this->entityManager->find(Listing::class, $id);
    }
}
