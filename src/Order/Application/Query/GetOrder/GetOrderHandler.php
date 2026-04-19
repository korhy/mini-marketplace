<?php

declare(strict_types=1);

namespace App\Order\Application\Query\GetOrder;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Repository\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetOrderQuery $query): OrderViewModel
    {
        $order = $this->repository->findById(
            OrderId::fromString($query->orderId),
        );

        if (null === $order) {
            throw OrderNotFoundException::forOrder($query->orderId);
        }

        return new OrderViewModel(
            id: (string) $order->id(),
            buyerId: (string) $order->buyerId(),
            listingId: (string) $order->listingId(),
            totalPrice: $order->totalPrice()->amount(),
            currency: $order->totalPrice()->currency(),
            status: $order->status()->value,
        );
    }
}
