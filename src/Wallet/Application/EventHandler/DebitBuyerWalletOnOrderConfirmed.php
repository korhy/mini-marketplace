<?php

declare(strict_types=1);

namespace App\Wallet\Application\EventHandler;

use App\Shared\Domain\IntegrationEvent\OrderConfirmedIntegrationEvent;
use App\Wallet\Application\Command\DebitWallet\DebitWalletCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class DebitBuyerWalletOnOrderConfirmed
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(OrderConfirmedIntegrationEvent $event): void
    {
        $this->commandBus->dispatch(new DebitWalletCommand(
            (string) $event->buyerId,
            $event->totalPrice->amount(),
            $event->totalPrice->currency(),
        ));
    }
}
