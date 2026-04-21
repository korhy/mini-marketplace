<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Entity;

use App\Shared\Domain\AggregateRoot;
use App\Shared\Domain\ValueObject\Money;
use App\Wallet\Domain\Event\FundsCredited;
use App\Wallet\Domain\Event\FundsDebited;
use App\Wallet\Domain\Exception\InsufficientFundsException;
use App\Wallet\Domain\ValueObject\Balance;
use App\Wallet\Domain\ValueObject\WalletId;

class Wallet extends AggregateRoot
{
    /** @var Transaction[] */
    private array $transactions = [];

    public function __construct(
        private WalletId $id,
        private Balance $balance,
    ) {
    }

    public function credit(Money $amount): void
    {
        $this->balance = $this->balance->increase($amount);
        $this->transactions[] = Transaction::credit($amount, 'Funds credited');

        $this->recordEvent(new FundsCredited(
            $this->id,
            $amount,
        ));
    }

    public function debit(Money $amount): void
    {
        if ($this->balance->isLessThan($amount)) {
            throw InsufficientFundsException::forWallet((string) $this->id);
        }

        $this->balance = $this->balance->decrease($amount);
        $this->transactions[] = Transaction::debit($amount, 'Funds debited');
        $this->recordEvent(new FundsDebited(
            $this->id,
            $amount,
        ));
    }

    public function id(): WalletId
    {
        return $this->id;
    }

    public function balance(): Balance
    {
        return $this->balance;
    }

    /**
     * @return Transaction[]
     */
    public function transactions(): array
    {
        return $this->transactions;
    }
}
