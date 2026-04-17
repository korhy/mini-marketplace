<?php

declare(strict_types=1);

namespace App\Listing\Application\Command\PublishListing;

use App\Listing\Domain\Exception\ListingNotFoundException;
use App\Listing\Domain\Repository\ListingRepositoryInterface;
use App\Shared\Domain\ValueObject\ListingId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class PublishListingHandler
{
    public function __construct(
        private ListingRepositoryInterface $repository,
    ) {
    }

    public function __invoke(PublishListingCommand $command): void
    {
        $listing = $this->repository->findById(
            ListingId::fromString($command->listingId),
        );

        if (null === $listing) {
            throw new ListingNotFoundException($command->listingId);
        }

        $listing->publish();

        $this->repository->save($listing);
    }
}
