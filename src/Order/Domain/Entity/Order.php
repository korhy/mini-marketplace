<?php

declare(strict_types=1);

namespace App\Order\Domain\Entity;

use App\Shared\Domain\ValueObject\ListingId;
use App\Order\Domain\ValueObject\BuyerId;
use App\Order\Domain\ValueObject\OrderId;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\Money;

class Order extends AggregateRoot
{
    public function __construct(
        private OrderId $id,
        private BuyerId $buyerId,
        private ListingId $listingId,
        private Money $totalPrice,
        private OrderStatus $status = OrderStatus::PENDING
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
        if ($this->status !== OrderStatus::PENDING) {
            /* 
            * @TODO: throw specific exceptions for invalid state transitions 
            */
            throw new \LogicException('Only pending orders can be confirmed');
        }

        $this->status = OrderStatus::CONFIRMED;
    }

    public function cancel(): void
    {
        if ($this->status !== OrderStatus::PENDING) {
            /* 
            * @TODO: throw specific exceptions for invalid state transitions 
            */
            throw new \LogicException('Only pending orders can be cancelled');
        }

        $this->status = OrderStatus::CANCELLED;
    }
}
