<?php

declare(strict_types=1);

namespace App\Wallet\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;

final readonly class Balance
{
    public function __construct(
        private Money $balance,
    ) {
        if ($balance->amount() < 0) {
            throw new \InvalidArgumentException('Balance cannot be negative');
        }
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function currency(): string
    {
        return $this->balance->currency();
    }

    public function increase(Money $amount): self
    {
        return new self($this->balance->add($amount));
    }

    public function decrease(Money $amount): self
    {
        if ($this->balance->amount() < $amount->amount()) {
            throw new \InvalidArgumentException('Insufficient balance');
        }

        return new self($this->balance->subtract($amount));
    }

    public function equals(self $other): bool
    {
        return $this->balance->amount() === $other->balance->amount()
               && $this->balance->currency() === $other->balance->currency();
    }

    public function isZero(): bool
    {
        return 0 === $this->balance->amount();
    }

    public function isGreaterThanOrEqual(Money $other): bool
    {
        if ($this->balance->currency() !== $other->currency()) {
            throw new \InvalidArgumentException('Cannot compare balances with different currencies');
        }

        return $this->balance->amount() >= $other->amount();
    }

    public function isLessThan(Money $other): bool
    {
        if ($this->balance->currency() !== $other->currency()) {
            throw new \InvalidArgumentException('Cannot compare balances with different currencies');
        }

        return $this->balance->amount() < $other->amount();
    }

    public function __toString(): string
    {
        return sprintf('%d %s', $this->balance->amount(), $this->balance->currency());
    }
}
