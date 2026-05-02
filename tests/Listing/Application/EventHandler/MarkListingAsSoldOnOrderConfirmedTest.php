<?php

declare(strict_types=1);

namespace App\Tests\Listing\Application\EventHandler;

use App\Listing\Application\EventHandler\MarkListingAsSoldOnOrderConfirmed;
use App\Listing\Domain\Entity\Listing;
use App\Listing\Domain\Exception\ListingNotAvailableException;
use App\Listing\Domain\Exception\ListingNotFoundException;
use App\Listing\Domain\Repository\ListingRepositoryInterface;
use App\Listing\Domain\ValueObject\Condition;
use App\Listing\Domain\ValueObject\ListingDescription;
use App\Listing\Domain\ValueObject\ListingStatus;
use App\Listing\Domain\ValueObject\ListingTitle;
use App\Listing\Domain\ValueObject\SellerId;
use App\Shared\Domain\IntegrationEvent\OrderConfirmedIntegrationEvent;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class MarkListingAsSoldOnOrderConfirmedTest extends TestCase
{
    private function createPublishedListing(ListingId $id): Listing
    {
        $listing = Listing::create(
            SellerId::generate(),
            new ListingTitle('A listing'),
            new ListingDescription('A description long enough.'),
            new Money(100, 'EUR'),
            Condition::NEW,
        );

        $reflection = new \ReflectionProperty(Listing::class, 'id');
        $reflection->setValue($listing, $id);

        $listing->publish();

        return $listing;
    }

    public function testItMarksListingAsSoldOnOrderConfirmed(): void
    {
        $listingId = ListingId::generate();
        $listing = $this->createPublishedListing($listingId);

        $repository = $this->createMock(ListingRepositoryInterface::class);
        $repository->expects($this->once())->method('findById')->with($listingId)->willReturn($listing);
        $repository->expects($this->once())->method('save')->with($listing);

        $handler = new MarkListingAsSoldOnOrderConfirmed($repository);
        $handler(new OrderConfirmedIntegrationEvent($listingId, UserId::generate(), new Money(100, 'EUR')));

        $this->assertSame(ListingStatus::SOLD, $listing->status());
    }

    public function testItThrowsListingNotFoundExceptionWhenListingDoesNotExist(): void
    {
        $listingId = ListingId::generate();

        $repository = $this->createStub(ListingRepositoryInterface::class);
        $repository->method('findById')->willReturn(null);

        $handler = new MarkListingAsSoldOnOrderConfirmed($repository);

        $this->expectException(ListingNotFoundException::class);
        $handler(new OrderConfirmedIntegrationEvent($listingId, UserId::generate(), new Money(100, 'EUR')));
    }

    public function testItThrowsListingNotAvailableExceptionWhenListingIsNotPublished(): void
    {
        $listingId = ListingId::generate();

        $listing = Listing::create(
            SellerId::generate(),
            new ListingTitle('A listing'),
            new ListingDescription('A description long enough.'),
            new Money(100, 'EUR'),
            Condition::NEW,
        );

        $reflection = new \ReflectionProperty(Listing::class, 'id');
        $reflection->setValue($listing, $listingId);

        $repository = $this->createStub(ListingRepositoryInterface::class);
        $repository->method('findById')->willReturn($listing);

        $handler = new MarkListingAsSoldOnOrderConfirmed($repository);

        $this->expectException(ListingNotAvailableException::class);
        $handler(new OrderConfirmedIntegrationEvent($listingId, UserId::generate(), new Money(100, 'EUR')));
    }
}
