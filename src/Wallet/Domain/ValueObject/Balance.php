<?php

declare(strict_types=1);

namespace App\Wallet\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
readonly class Balance
{
    #[ORM\Column(type: 'integer')]
    private int $amount; // Amount in cents to avoid floating point issues

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency; // ISO 4217 currency code

    public function __construct(
        int $amount,
        string $currency,
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Balance cannot be negative');
        }
        if (!in_array($currency, ['USD', 'EUR', 'GBP'])) { // Example of supported currencies
            throw new \InvalidArgumentException('Unsupported currency: '.$currency);
        }

        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function toMoney(): Money
    {
        return new Money($this->amount, $this->currency);
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function increase(Money $amount): self
    {
        return new self($this->amount + $amount->amount(), $this->currency);
    }

    public function decrease(Money $amount): self
    {
        if ($this->amount < $amount->amount()) {
            throw new \InvalidArgumentException('Insufficient balance');
        }

        return new self($this->amount - $amount->amount(), $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
               && $this->currency === $other->currency;
    }

    public function isZero(): bool
    {
        return 0 === $this->amount;
    }

    public function isGreaterThanOrEqual(Money $other): bool
    {
        if ($this->currency !== $other->currency()) {
            throw new \InvalidArgumentException('Cannot compare balances with different currencies');
        }

        return $this->amount >= $other->amount();
    }

    public function isLessThan(Money $other): bool
    {
        if ($this->currency !== $other->currency()) {
            throw new \InvalidArgumentException('Cannot compare balances with different currencies');
        }

        return $this->amount < $other->amount();
    }

    public function __toString(): string
    {
        return sprintf('%d %s', $this->amount, $this->currency);
    }
}
