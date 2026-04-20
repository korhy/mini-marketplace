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
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order extends AggregateRoot
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'order_id')]
        private OrderId $id,

        #[ORM\Column(type: 'buyer_id')]
        private BuyerId $buyerId,

        #[ORM\Column(type: 'listing_id')]
        private ListingId $listingId,

        #[ORM\Embedded(class: Money::class)]
        private Money $totalPrice,

        #[ORM\Column(type: 'string', enumType: OrderStatus::class)]
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
            $order->id(),
            $order->buyerId(),
            $order->listingId(),
            $order->totalPrice(),
        ));

        return $order;
    }

    public function confirm(): void
    {
        if (OrderStatus::PENDING !== $this->status) {
            throw OrderCannotBeConfirmedException::forOrder($this->id()->__toString());
        }

        $this->status = OrderStatus::CONFIRMED;
        $this->recordEvent(new OrderConfirmed(
            $this->id(),
        ));
    }

    public function cancel(): void
    {
        if (OrderStatus::PENDING !== $this->status) {
            throw OrderCannotBeCancelledException::forOrder($this->id()->__toString());
        }

        $this->status = OrderStatus::CANCELLED;
        $this->recordEvent(new OrderCancelled(
            $this->id(),
        ));
    }

    public function id(): OrderId
    {
        return $this->id;
    }

    public function buyerId(): BuyerId
    {
        return $this->buyerId;
    }

    public function listingId(): ListingId
    {
        return $this->listingId;
    }

    public function totalPrice(): Money
    {
        return $this->totalPrice;
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }
}
