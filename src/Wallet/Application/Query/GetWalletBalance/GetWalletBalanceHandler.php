<?php

declare(strict_types=1);

namespace App\Wallet\Application\Query\GetWalletBalance;

use App\Wallet\Domain\Exception\WalletNotFoundException;
use App\Wallet\Domain\Repository\WalletRepositoryInterface;
use App\Wallet\Domain\ValueObject\WalletId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetWalletBalanceHandler
{
    public function __construct(
        private WalletRepositoryInterface $repository,
    ) {
    }

    public function __invoke(GetWalletBalanceQuery $query): GetWalletBalanceViewModel
    {
        $wallet = $this->repository->findById(
            WalletId::fromString($query->walletId),
        );

        if (null === $wallet) {
            throw WalletNotFoundException::withId($query->walletId);
        }

        return new GetWalletBalanceViewModel(
            id: (string) $wallet->id(),
            balance: $wallet->balance()->toMoney()->amount(),
            currency: $wallet->balance()->currency(),
        );
    }
}
