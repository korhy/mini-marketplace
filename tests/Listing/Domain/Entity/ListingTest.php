<?php

declare(strict_types=1);

namespace App\Tests\Listing\Domain\Entity;

use App\Listing\Domain\Entity\Listing;
use App\Listing\Domain\Event\ListingPublished;
use App\Listing\Domain\Event\ListingSold;
use App\Listing\Domain\Exception\ListingAlreadyPublishedException;
use App\Listing\Domain\Exception\ListingNotAvailableException;
use App\Listing\Domain\ValueObject\Condition;
use App\Listing\Domain\ValueObject\ListingDescription;
use App\Listing\Domain\ValueObject\ListingStatus;
use App\Listing\Domain\ValueObject\ListingTitle;
use App\Listing\Domain\ValueObject\SellerId;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class ListingTest extends TestCase
{
    // --- Helper ---

    private function createListing(): Listing
    {
        return Listing::create(
            SellerId::generate(),
            new ListingTitle('Test Listing'),
            new ListingDescription('This is a test listing description.'),
            new Money(100, 'EUR'),
            Condition::NEW,
        );
    }

    // --- Création ---

    public function testCreateListingStartsAsDraft(): void
    {
        $listing = $this->createListing();

        $this->assertEquals(ListingStatus::DRAFT, $listing->status());
        $this->assertEmpty($listing->pullEvents());
    }

    // --- publish() ---

    public function testPublishChangesStatusToPublished(): void
    {
        $listing = $this->createListing();

        $listing->publish();

        $this->assertEquals(ListingStatus::PUBLISHED, $listing->status());
    }

    public function testPublishRecordsListingPublishedEvent(): void
    {
        $listing = $this->createListing();

        $listing->publish();

        $events = $listing->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ListingPublished::class, $events[0]);
    }

    public function testPublishThrowsIfAlreadyPublished(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->pullEvents(); // empty the event queue

        $this->expectException(ListingAlreadyPublishedException::class);
        $listing->publish();
    }

    public function testPublishThrowsIfAlreadySold(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->markAsSold();
        $listing->pullEvents(); // empty the event queue

        $this->expectException(ListingAlreadyPublishedException::class);
        $listing->publish();
    }

    // --- markAsSold() ---

    public function testMarkAsSoldChangesStatusToSold(): void
    {
        $listing = $this->createListing();
        $listing->publish();

        $listing->markAsSold();

        $this->assertEquals(ListingStatus::SOLD, $listing->status());
    }

    public function testMarkAsSoldRecordsListingSoldEvent(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->pullEvents(); // empty the event queue

        $listing->markAsSold();

        $events = $listing->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ListingSold::class, $events[0]);
    }

    public function testMarkAsSoldThrowsIfDraft(): void
    {
        $listing = $this->createListing();

        $this->expectException(ListingNotAvailableException::class);
        $listing->markAsSold();
    }

    public function testMarkAsSoldThrowsIfAlreadySold(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->markAsSold();
        $listing->pullEvents(); // empty the event queue

        $this->expectException(ListingNotAvailableException::class);
        $listing->markAsSold();
    }

    // --- pullEvents() ---

    public function testPullEventsClearsTheEventList(): void
    {
        $listing = $this->createListing();
        $listing->publish();

        $listing->pullEvents();

        $this->assertEmpty($listing->pullEvents());
    }
}
