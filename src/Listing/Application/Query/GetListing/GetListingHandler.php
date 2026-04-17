<?php

declare(strict_types=1);

namespace App\Listing\Application\Query\GetListing;

use App\Listing\Domain\Exception\ListingNotFoundException;
use App\Listing\Domain\Repository\ListingRepositoryInterface;
use App\Shared\Domain\ValueObject\ListingId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetListingHandler
{
    public function __construct(
        private ListingRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetListingQuery $query): ListingViewModel
    {
        $listing = $this->repository->findById(
            ListingId::fromString($query->listingId),
        );

        if (null === $listing) {
            throw ListingNotFoundException::forId($query->listingId);
        }

        return new ListingViewModel(
            id: (string) $listing->id(),
            sellerId: (string) $listing->sellerId(),
            title: (string) $listing->title(),
            description: (string) $listing->description(),
            price: $listing->price()->amount(),
            currency: $listing->price()->currency(),
            condition: $listing->condition()->value,
            status: $listing->status()->value,
        );
    }
}
