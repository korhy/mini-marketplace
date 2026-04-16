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

    public function test_create_listing_starts_as_draft(): void
    {
        $listing = $this->createListing();

        $this->assertEquals(ListingStatus::DRAFT, $listing->status());
        $this->assertEmpty($listing->pullEvents());
    }

    // --- publish() ---

    public function test_publish_changes_status_to_published(): void
    {
        $listing = $this->createListing();

        $listing->publish();

        $this->assertEquals(ListingStatus::PUBLISHED, $listing->status());
    }

    public function test_publish_records_listing_published_event(): void
    {
        $listing = $this->createListing();

        $listing->publish();

        $events = $listing->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ListingPublished::class, $events[0]);
    }

    public function test_publish_throws_if_already_published(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->pullEvents(); // empty the event queue

        $this->expectException(ListingAlreadyPublishedException::class);
        $listing->publish();
    }

    public function test_publish_throws_if_already_sold(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->markAsSold();
        $listing->pullEvents(); // empty the event queue

        $this->expectException(ListingAlreadyPublishedException::class);
        $listing->publish();
    }

    // --- markAsSold() ---

    public function test_mark_as_sold_changes_status_to_sold(): void
    {
        $listing = $this->createListing();
        $listing->publish();

        $listing->markAsSold();

        $this->assertEquals(ListingStatus::SOLD, $listing->status());
    }

    public function test_mark_as_sold_records_listing_sold_event(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->pullEvents(); // empty the event queue

        $listing->markAsSold();

        $events = $listing->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(ListingSold::class, $events[0]);
    }

    public function test_mark_as_sold_throws_if_draft(): void
    {
        $listing = $this->createListing();

        $this->expectException(ListingNotAvailableException::class);
        $listing->markAsSold();
    }

    public function test_mark_as_sold_throws_if_already_sold(): void
    {
        $listing = $this->createListing();
        $listing->publish();
        $listing->markAsSold();
        $listing->pullEvents(); // empty the event queue

        $this->expectException(ListingNotAvailableException::class);
        $listing->markAsSold();
    }

    // --- pullEvents() ---

    public function test_pull_events_clears_the_event_list(): void
    {
        $listing = $this->createListing();
        $listing->publish();

        $listing->pullEvents();

        $this->assertEmpty($listing->pullEvents());
    }
}
