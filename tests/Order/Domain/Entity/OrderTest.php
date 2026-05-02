<?php

declare(strict_types=1);

namespace App\Tests\Order\Domain\Entity;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Event\OrderCancelled;
use App\Order\Domain\Event\OrderConfirmed;
use App\Order\Domain\Event\OrderPlaced;
use App\Order\Domain\Exception\OrderCannotBeCancelledException;
use App\Order\Domain\Exception\OrderCannotBeConfirmedException;
use App\Order\Domain\ValueObject\BuyerId;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Shared\Domain\IntegrationEvent\OrderConfirmedIntegrationEvent;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    // --- Helper ---
    private function createOrder(): Order
    {
        $order = Order::place(
            listingId: ListingId::generate(),
            buyerId: BuyerId::generate(),
            price: new Money(100, 'EUR'),
        );

        return $order;
    }

    // --- Place ---

    public function testPlaceOrdersStartAsPending(): void
    {
        $order = $this->createOrder();

        $this->assertEquals(OrderStatus::PENDING, $order->status());
    }

    public function testPlaceRecordsOrderPlacedEvent(): void
    {
        $order = $this->createOrder();

        $events = $order->pullEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf(OrderPlaced::class, $events[0]);
    }

    // --- Confirm ---

    public function testConfirmChangesStatusToConfirmed(): void
    {
        $order = $this->createOrder();

        $order->confirm();

        $this->assertEquals(OrderStatus::CONFIRMED, $order->status());
    }

    public function testConfirmOrderCannotBeCancelled(): void
    {
        $order = $this->createOrder();

        $order->confirm();

        $this->expectException(OrderCannotBeCancelledException::class);

        $order->cancel();
    }

    public function testConfirmThrowsIfNotPending(): void
    {
        $order = $this->createOrder();

        $order->confirm();
        $order->pullEvents();

        $this->expectException(OrderCannotBeConfirmedException::class);

        $order->confirm();
    }

    public function testConfirmThrowsIfCancelled(): void
    {
        $order = $this->createOrder();

        $order->cancel();
        $order->pullEvents();

        $this->expectException(OrderCannotBeConfirmedException::class);

        $order->confirm();
    }

    public function testConfirmRecordsOrderConfirmedEvent(): void
    {
        $order = $this->createOrder();

        $order->confirm();

        $events = $order->pullEvents();
        $this->assertCount(3, $events);
        $this->assertInstanceOf(OrderConfirmed::class, $events[1]);
        $this->assertInstanceOf(OrderConfirmedIntegrationEvent::class, $events[2]);
    }

    // --- Cancel ---

    public function testCancelChangesStatusToCancelled(): void
    {
        $order = $this->createOrder();

        $order->cancel();

        $this->assertEquals(OrderStatus::CANCELLED, $order->status());
    }

    public function testCancelRecordsOrderCancelledEvent(): void
    {
        $order = $this->createOrder();

        $order->cancel();

        $events = $order->pullEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(OrderCancelled::class, $events[1]);
    }

    public function testCancelOrderCannotBeConfirmed(): void
    {
        $order = $this->createOrder();

        $order->cancel();

        $this->expectException(OrderCannotBeConfirmedException::class);

        $order->confirm();
    }

    public function testCancelThrowsIfAlreadyCancelled(): void
    {
        $order = $this->createOrder();

        $order->cancel();
        $order->pullEvents();

        $this->expectException(OrderCannotBeCancelledException::class);

        $order->cancel();
    }
}
