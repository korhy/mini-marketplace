<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Order\Domain\ValueObject\BuyerId;
use App\Order\Domain\ValueObject\OrderId;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;

class Order extends AggregateRoot
{
    public function __construct(
        private OrderId $id,
        private BuyerId $buyerId,
        private ListingId $listingId,
        private Money $totalPrice,
        private OrderStatus $status = OrderStatus::PENDING,
    ) {}

    public static function place(
        ListingId $listingId,
        BuyerId $buyerId,
        Money $price,
    ): self {
        return new self(OrderId::generate(), $buyerId, $listingId, $price);
    }

    public function confirm(): void
    {
        if (OrderStatus::PENDING !== $this->status) {
            /*
            * @TODO: throw specific exceptions for invalid state transitions
            */
            throw new \LogicException('Only pending orders can be confirmed');
        }

        $this->status = OrderStatus::CONFIRMED;
    }

    public function cancel(): void
    {
        if (OrderStatus::PENDING !== $this->status) {
            /*
            * @TODO: throw specific exceptions for invalid state transitions
            */
            throw new \LogicException('Only pending orders can be cancelled');
        }

        $this->status = OrderStatus::CANCELLED;
    }

    public function getId(): OrderId
    {
        return $this->id;
    }

    public function getBuyerId(): BuyerId
    {
        return $this->buyerId;
    }

    public function getListingId(): ListingId
    {
        return $this->listingId;
    }

    public function getTotalPrice(): Money
    {
        return $this->totalPrice;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }
}
