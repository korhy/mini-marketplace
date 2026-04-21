<?php

declare(strict_types=1);

namespace App\Wallet\Application\Query\GetTransactionHistory;

use App\Wallet\Domain\Exception\WalletNotFoundException;
use App\Wallet\Domain\Repository\WalletRepositoryInterface;
use App\Wallet\Domain\ValueObject\WalletId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetTransactionHistoryHandler
{
    public function __construct(
        private WalletRepositoryInterface $repository,
    ) {
    }

    /**
     * @return GetTransactionHistoryViewModel[]
     */
    public function __invoke(GetTransactionHistoryQuery $query): array
    {
        $wallet = $this->repository->findById(
            WalletId::fromString($query->walletId)
        );

        if (null === $wallet) {
            throw WalletNotFoundException::withId($query->walletId);
        }

        $transactions = $wallet->transactions();

        return array_map(
            fn ($transaction) => new GetTransactionHistoryViewModel(
                id: (string) $transaction->id(),
                amount: $transaction->amount()->amount(),
                currency: $transaction->amount()->currency(),
                type: $transaction->type()->value,
                createdAt: $transaction->createdAt()->format(\DateTimeInterface::ATOM),
            ),
            $transactions
        );
    }
}
