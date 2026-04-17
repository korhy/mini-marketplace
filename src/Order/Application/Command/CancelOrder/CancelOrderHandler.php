<?php

declare(strict_types=1);

namespace App\Order\Application\Command\CancelOrder;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Repository\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CancelOrderHandler
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function __invoke(CancelOrderCommand $command): void
    {
        $order = $this->orderRepository->findById(new OrderId($command->orderId));

        if (null === $order) {
            throw OrderNotFoundException::forOrder($command->orderId);
        }

        $order->cancel();

        $this->orderRepository->save($order);
    }
}
