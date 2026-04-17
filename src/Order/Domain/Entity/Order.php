<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Order\Domain\Event\OrderCancelled;
use App\Order\Domain\Event\OrderConfirmed;
use App\Order\Domain\Event\OrderPlaced;
use App\Order\Domain\Exception\OrderCannotBeCancelledException;
use App\Order\Domain\Exception\OrderCannotBeConfirmedException;
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
    ) {
    }

    public static function place(
        ListingId $listingId,
        BuyerId $buyerId,
        Money $price,
    ): self {
        $order = new self(OrderId::generate(), $buyerId, $listingId, $price);

        $order->recordEvent(new OrderPlaced(
            $order->getId(),
            $order->getBuyerId(),
            $order->getListingId(),
            $order->getTotalPrice(),
        ));

        return $order;
    }

    public function confirm(): void
    {
        if (OrderStatus::PENDING !== $this->status) {
            throw OrderCannotBeConfirmedException::forOrder($this->getId()->__toString());
        }

        $this->status = OrderStatus::CONFIRMED;
        $this->recordEvent(new OrderConfirmed(
            $this->getId(),
        ));
    }

    public function cancel(): void
    {
        if (OrderStatus::CONFIRMED === $this->status) {
            throw OrderCannotBeCancelledException::forOrder($this->getId()->__toString());
        }

        $this->status = OrderStatus::CANCELLED;
        $this->recordEvent(new OrderCancelled(
            $this->getId(),
        ));
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
