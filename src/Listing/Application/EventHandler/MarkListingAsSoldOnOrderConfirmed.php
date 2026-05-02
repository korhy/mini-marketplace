<?php

declare(strict_types=1);

namespace App\Listing\Application\EventHandler;

use App\Listing\Domain\Exception\ListingNotFoundException;
use App\Listing\Domain\Repository\ListingRepositoryInterface;
use App\Shared\Domain\IntegrationEvent\OrderConfirmedIntegrationEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class MarkListingAsSoldOnOrderConfirmed
{
    public function __construct(
        private ListingRepositoryInterface $listingRepository,
    ) {
    }

    public function __invoke(OrderConfirmedIntegrationEvent $event): void
    {
        $listing = $this->listingRepository->findById($event->listingId);

        if (null === $listing) {
            throw ListingNotFoundException::forId((string) $event->listingId);
        }

        $listing->markAsSold();

        $this->listingRepository->save($listing);
    }
}
