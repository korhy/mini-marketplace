<?php

declare(strict_types=1);

namespace App\Listing\Domain\Entity;

use App\Listing\Domain\Event\ListingPublished;
use App\Listing\Domain\Event\ListingSold;
use App\Listing\Domain\Exception\ListingAlreadyPublishedException;
use App\Listing\Domain\Exception\ListingNotAvailableException;
use App\Listing\Domain\ValueObject\Condition;
use App\Listing\Domain\ValueObject\ListingDescription;
use App\Listing\Domain\ValueObject\ListingStatus;
use App\Listing\Domain\ValueObject\ListingTitle;
use App\Listing\Domain\ValueObject\SellerId;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'listings')]
class Listing extends AggregateRoot
{
    #[ORM\Column(type: 'string', enumType: ListingStatus::class)]
    private ListingStatus $status;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'listing_id')]
        private ListingId $id,

        #[ORM\Column(type: 'seller_id')]
        private SellerId $sellerId,

        #[ORM\Column(type: 'listing_title')]
        private ListingTitle $title,

        #[ORM\Column(type: 'listing_description', length: 2000)]
        private ListingDescription $description,

        #[ORM\Embedded(class: Money::class)]
        private Money $price,

        #[ORM\Column(type: 'string', enumType: Condition::class)]
        private Condition $condition,
    ) {
        $this->status = ListingStatus::DRAFT;
    }

    public static function create(
        SellerId $sellerId,
        ListingTitle $title,
        ListingDescription $description,
        Money $price,
        Condition $condition,
    ): self {
        return new self(
            ListingId::generate(),
            $sellerId,
            $title,
            $description,
            $price,
            $condition,
        );
    }

    public function publish(): void
    {
        if (ListingStatus::DRAFT !== $this->status) {
            throw ListingAlreadyPublishedException::forListing((string) $this->id);
        }

        $this->status = ListingStatus::PUBLISHED;
        $this->recordEvent(new ListingPublished($this->id, $this->sellerId));
    }

    public function markAsSold(): void
    {
        if (ListingStatus::PUBLISHED !== $this->status) {
            throw ListingNotAvailableException::forListing((string) $this->id);
        }

        $this->status = ListingStatus::SOLD;
        $this->recordEvent(new ListingSold($this->id, $this->sellerId));
    }

    public function id(): ListingId
    {
        return $this->id;
    }

    public function sellerId(): SellerId
    {
        return $this->sellerId;
    }

    public function title(): ListingTitle
    {
        return $this->title;
    }

    public function description(): ListingDescription
    {
        return $this->description;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function condition(): Condition
    {
        return $this->condition;
    }

    public function status(): ListingStatus
    {
        return $this->status;
    }
}
