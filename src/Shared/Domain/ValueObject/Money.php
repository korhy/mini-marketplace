<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
readonly class Money
{
    #[ORM\Column(type: 'integer')]
    private int $amount; // Amount in cents to avoid floating point issues

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency; // ISO 4217 currency code

    public function __construct(int $amount, string $currency)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }

        if (!in_array($currency, ['USD', 'EUR', 'GBP'])) { // Example of supported currencies
            throw new \InvalidArgumentException('Unsupported currency: '.$currency);
        }

        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot add money with different currencies');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot subtract money with different currencies');
        }
        if ($this->amount < $other->amount) {
            throw new \InvalidArgumentException('Resulting amount cannot be negative');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }
}
