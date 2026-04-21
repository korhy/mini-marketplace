<?php

declare(strict_types=1);

namespace App\Wallet\Domain\Entity;

use App\Shared\Domain\ValueObject\Money;
use App\Wallet\Domain\ValueObject\TransactionId;
use App\Wallet\Domain\ValueObject\TransactionType;

final class Transaction
{
    private function __construct(
        private readonly TransactionId $id,
        private readonly Money $amount,
        private readonly TransactionType $type,
        private readonly \DateTimeImmutable $createdAt,
        private readonly string $description,
    ) {
    }

    public static function credit(Money $amount, string $description): self
    {
        return new self(TransactionId::generate(), $amount, TransactionType::CREDIT, new \DateTimeImmutable(), $description);
    }

    public static function debit(Money $amount, string $description): self
    {
        return new self(TransactionId::generate(), $amount, TransactionType::DEBIT, new \DateTimeImmutable(), $description);
    }

    public function id(): TransactionId
    {
        return $this->id;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function type(): TransactionType
    {
        return $this->type;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function description(): string
    {
        return $this->description;
    }
}
