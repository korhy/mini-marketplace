<?php

declare(strict_types=1);

namespace App\Listing\Application\Command\CreateListing;

use App\Listing\Domain\Entity\Listing;
use App\Listing\Domain\Repository\ListingRepositoryInterface;
use App\Listing\Domain\ValueObject\Condition;
use App\Listing\Domain\ValueObject\ListingDescription;
use App\Listing\Domain\ValueObject\ListingTitle;
use App\Listing\Domain\ValueObject\SellerId;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class CreateListingHandler
{
    public function __construct(
        private ListingRepositoryInterface $listingRepository,
    ) {
    }

    public function __invoke(CreateListingCommand $command): void
    {
        $listing = Listing::create(
            new SellerId($command->sellerId),
            new ListingTitle($command->title),
            new ListingDescription($command->description),
            new Money($command->price, $command->currency),
            Condition::from($command->condition),
        );

        $this->listingRepository->save($listing);
    }
}
