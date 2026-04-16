<?php

declare(strict_types=1);

namespace App\Listing\Domain\Repository;

use App\Listing\Domain\Entity\Listing;
use App\Listing\Domain\ValueObject\ListingId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineListingRepository implements ListingRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function save(Listing $listing): void
    {
        $this->entityManager->persist($listing);
        $this->entityManager->flush();
    }

    public function findById(ListingId $id): ?Listing
    {
        return $this->entityManager->find(Listing::class, $id);
    }
}
