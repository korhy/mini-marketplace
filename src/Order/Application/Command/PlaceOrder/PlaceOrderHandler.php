<?php

declare(strict_types=1);

namespace App\Order\Application\Command\PlaceOrder;

use App\Listing\Domain\Exception\ListingNotAvailableException;
use App\Order\Domain\Entity\Order;
use App\Order\Domain\Port\ListingAvailabilityChecker;
use App\Order\Domain\Repository\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\BuyerId;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final class PlaceOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ListingAvailabilityChecker $listingAvailabilityChecker,
    ) {
    }

    public function __invoke(PlaceOrderCommand $command): void
    {
        $listingId = new ListingId($command->listingId);

        if (!$this->listingAvailabilityChecker->isAvailable($listingId)) {
            throw ListingNotAvailableException::forListing($listingId->__toString());
        }
        $order = Order::place(
            new ListingId($command->listingId),
            new BuyerId($command->buyerId),
            new Money($command->price, $command->currency)
        );

        $this->orderRepository->save($order);
    }
}
