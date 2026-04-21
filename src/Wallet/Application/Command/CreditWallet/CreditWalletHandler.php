<?php

declare(strict_types=1);

namespace App\Wallet\Application\Command\CreditWallet;

use App\Shared\Domain\ValueObject\Money;
use App\Wallet\Domain\Exception\WalletNotFoundException;
use App\Wallet\Domain\Repository\WalletRepositoryInterface;
use App\Wallet\Domain\ValueObject\WalletId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreditWalletHandler
{
    public function __construct(
        private WalletRepositoryInterface $walletRepository,
    ) {
    }

    public function __invoke(CreditWalletCommand $command): void
    {
        $wallet = $this->walletRepository->findById(new WalletId($command->walletId));

        if (null === $wallet) {
            throw WalletNotFoundException::withId($command->walletId);
        }

        $wallet->credit(new Money($command->amount, $command->currency));

        $this->walletRepository->save($wallet);
    }
}
