<?php

declare(strict_types=1);

namespace App\Tests\Wallet\Application\EventHandler;

use App\Shared\Domain\IntegrationEvent\OrderConfirmedIntegrationEvent;
use App\Shared\Domain\ValueObject\ListingId;
use App\Shared\Domain\ValueObject\Money;
use App\Shared\Domain\ValueObject\UserId;
use App\Wallet\Application\Command\DebitWallet\DebitWalletCommand;
use App\Wallet\Application\EventHandler\DebitBuyerWalletOnOrderConfirmed;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class DebitBuyerWalletOnOrderConfirmedTest extends TestCase
{
    public function testItDispatchesDebitWalletCommand(): void
    {
        $buyerId = UserId::generate();
        $totalPrice = new Money(5000, 'EUR');

        $event = new OrderConfirmedIntegrationEvent(
            ListingId::generate(),
            $buyerId,
            $totalPrice,
        );

        $commandBus = $this->createMock(MessageBusInterface::class);
        $commandBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (DebitWalletCommand $command) use ($buyerId, $totalPrice): bool {
                return $command->walletId === (string) $buyerId
                    && $command->amount === $totalPrice->amount()
                    && $command->currency === $totalPrice->currency();
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $handler = new DebitBuyerWalletOnOrderConfirmed($commandBus);
        $handler($event);
    }
}
